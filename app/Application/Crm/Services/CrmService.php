<?php

namespace App\Application\Crm\Services;

use App\Application\Inscripciones\Services\InscripcionCuotasService;
use App\Infrastructure\CampanasLeads\Models\Lead;
use App\Infrastructure\CampanasLeads\Models\LeadSeguimiento;
use App\Shared\Kernel\Support\PlanGratuito;
use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Facades\DB;

/**
 * Embudo de ventas. Un interesado (lead) entra por cualquier canal sin duplicarse,
 * se reparte por turnos entre los vendedores activos, avanza por etapas y se cierra
 * solo cuando se inscribe (por Caja, el portal o su panel).
 */
class CrmService
{
    public const ETAPAS = ['nuevo', 'contactado', 'interesado', 'por_pagar', 'inscrito', 'perdido'];
    public const ABIERTAS = ['nuevo', 'contactado', 'interesado', 'por_pagar'];
    public const ORIGENES = ['manual', 'excel', 'portal', 'contacto', 'whatsapp', 'whatsapp_grupo', 'meta', 'caja'];
    public const HORAS_SIN_ATENCION = 24;

    public function __construct(private readonly InscripcionCuotasService $cuotas) {}

    /**
     * Registra un interesado o suma el contacto al que ya está abierto (mismo celular, correo o carnet).
     *
     * @param array{nombre?: ?string, celular?: ?string, correo?: ?string, ci?: ?string, programa_id?: ?int,
     *              campana_lead_id?: ?int, campana_publicidad_id?: ?int, nota?: ?string, vendedor_id?: ?int} $datos
     */
    public function registrar(array $datos, string $origen): Lead
    {
        $celular = self::normalizarCelular($datos['celular'] ?? null);
        $correo  = isset($datos['correo']) ? mb_strtolower(trim((string) $datos['correo'])) ?: null : null;
        $ci      = isset($datos['ci']) ? mb_strtoupper(trim((string) $datos['ci'])) ?: null : null;

        if (! $celular && ! $correo && ! $ci) {
            throw new DomainException('El interesado necesita al menos un celular, correo o carnet.');
        }

        return DB::transaction(function () use ($datos, $origen, $celular, $correo, $ci) {
            $existente = $this->buscarAbierto($celular, $correo, $ci);

            if ($existente) {
                $existente->fill(array_filter([
                    'celular'     => $existente->celular ?: $celular,
                    'correo'      => $existente->correo ?: $correo,
                    'ci'          => $existente->ci ?: $ci,
                    'programa_id' => $existente->programa_id ?: ($datos['programa_id'] ?? null),
                ], fn ($v) => $v !== null))->save();

                $this->anotar($existente, 'otro', 'contactado', 'Volvió a escribir por '.self::nombreOrigen($origen).($datos['nota'] ?? null ? ': '.$datos['nota'] : '.'), null, sumaContacto: false);

                return $existente->fresh();
            }

            $lead = Lead::create([
                'campana_lead_id'       => $datos['campana_lead_id'] ?? null,
                'nombre'                => trim((string) ($datos['nombre'] ?? '')) ?: 'Sin nombre',
                'celular'               => $celular,
                'correo'                => $correo,
                'ci'                    => $ci,
                'estado'                => 'nuevo',
                'origen'                => in_array($origen, self::ORIGENES, true) ? $origen : 'manual',
                'programa_id'           => $datos['programa_id'] ?? null,
                'campana_publicidad_id' => $datos['campana_publicidad_id'] ?? null,
                'id_us'                 => $this->estudianteDe($celular, $correo, $ci),
            ]);

            $vendedor = $datos['vendedor_id'] ?? $this->siguienteVendedor();
            if ($vendedor) {
                $this->asignar($lead, (int) $vendedor);
            }
            if (! empty($datos['nota'])) {
                $this->anotar($lead, 'otro', 'contactado', $datos['nota'], null, sumaContacto: false);
            }

            return $lead->fresh();
        });
    }

    /** Reparto por turnos: el vendedor activo al que hace más tiempo no se le asigna nada. */
    public function siguienteVendedor(): ?int
    {
        // El id del último interesado asignado ordena sin empates (dos pueden llegar en el mismo segundo)
        $ultimos = Lead::query()->whereNotNull('vendedor_asignado_id')
            ->selectRaw('vendedor_asignado_id, MAX(id) as ultimo')
            ->groupBy('vendedor_asignado_id')
            ->pluck('ultimo', 'vendedor_asignado_id');

        return DB::table('vendedores')->where('activo', true)->whereNull('deleted_at')->orderBy('id')->pluck('id')
            ->sortBy(fn ($id) => (int) ($ultimos[$id] ?? 0))
            ->first();
    }

    public function asignar(Lead $lead, int $vendedorId): Lead
    {
        if (! DB::table('vendedores')->where('id', $vendedorId)->where('activo', true)->whereNull('deleted_at')->exists()) {
            throw new DomainException('Ese vendedor no existe o está inactivo.');
        }
        $lead->update(['vendedor_asignado_id' => $vendedorId, 'asignado_at' => now(), 'alerta_enviada_at' => null]);

        return $lead;
    }

    /** Mueve de etapa a mano. Perder exige motivo; "inscrito" solo lo marca una inscripción real. */
    public function moverEtapa(Lead $lead, string $etapa, ?string $motivo = null): Lead
    {
        if (! in_array($etapa, self::ETAPAS, true)) {
            throw new DomainException('Etapa no válida.');
        }
        if ($etapa === 'inscrito' && ! $lead->id_ins) {
            throw new DomainException('Pasa a "Inscrito" solo cuando se registra su inscripción (Caja, portal o su panel).');
        }
        if ($etapa === 'perdido' && ! trim((string) $motivo)) {
            throw new DomainException('Indica el motivo por el que se perdió.');
        }

        $lead->update([
            'estado'         => $etapa,
            'motivo_perdida' => $etapa === 'perdido' ? trim($motivo) : null,
            'cerrado_at'     => in_array($etapa, ['inscrito', 'perdido'], true) ? now() : null,
        ]);

        return $lead;
    }

    /** Contacto registrado por el vendedor: actualiza la etapa y la próxima acción. */
    public function anotar(Lead $lead, string $tipo, string $resultado, ?string $nota, ?string $proximaAccionAt, bool $sumaContacto = true, ?string $proximaAccion = null): LeadSeguimiento
    {
        $seg = LeadSeguimiento::create([
            'lead_id'              => $lead->id,
            'vendedor_id'          => auth()->id(),
            'tipo'                 => $tipo,
            'resultado'            => $resultado,
            'nota'                 => $nota,
            'proxima_accion'       => $proximaAccion,
            'fecha_proxima_accion' => $proximaAccionAt ? Carbon::parse($proximaAccionAt)->toDateString() : null,
        ]);

        if ($sumaContacto) {
            $cambios = ['ultimo_contacto_at' => now(), 'alerta_enviada_at' => null, 'proxima_accion_at' => $proximaAccionAt ? Carbon::parse($proximaAccionAt) : null];
            $avance = ['contactado' => 'contactado', 'interesado' => 'interesado'][$resultado] ?? null;
            // Solo avanza: un contacto nunca hace retroceder a quien ya está más adelante
            if ($avance && array_search($avance, self::ETAPAS, true) > array_search($lead->estado, self::ETAPAS, true) && in_array($lead->estado, self::ABIERTAS, true)) {
                $cambios['estado'] = $avance;
            }
            if ($resultado === 'no_interesado') {
                $cambios += ['estado' => 'perdido', 'motivo_perdida' => $nota ?: 'No le interesa', 'cerrado_at' => now()];
            }
            $lead->update($cambios);
        }

        return $seg;
    }

    /**
     * Una inscripción cierra el interesado abierto de esa persona: "inscrito" si ya pagó algo
     * (o es gratuita) y "por_pagar" mientras su pago no esté confirmado. Si no había interesado,
     * se crea uno para que la venta cuente en el embudo.
     */
    public function sincronizarConInscripcion(int $idIns): ?Lead
    {
        $ins = DB::table('t_inscripcion as i')
            ->leftJoin('t_usuario as u', function ($j) {
                $j->on('u.id_us', '=', 'i.id_us')
                  ->whereRaw('u.id_us_reg = (SELECT MIN(u2.id_us_reg) FROM t_usuario u2 WHERE u2.id_us = u.id_us)');
            })
            ->leftJoin('t_imparte as imp', 'imp.id_imp', '=', 'i.id_imp')
            ->where('i.id_ins', $idIns)->where('i.estado', 1)
            ->first(['i.id_ins', 'i.id_us', 'i.id_plan', 'i.origen', 'imp.id_mat', 'u.nombre', 'u.appaterno', 'u.ci', 'u.celular', 'u.email']);
        if (! $ins) {
            return null;
        }

        $pagoConfirmado = $this->cuotas->totalAplicable($idIns) > 0 || ($ins->id_plan && PlanGratuito::es((int) $ins->id_plan));
        $etapa = $pagoConfirmado ? 'inscrito' : 'por_pagar';

        $lead = Lead::where('id_ins', $idIns)->first()
            ?? Lead::whereIn('estado', self::ABIERTAS)->where('id_us', $ins->id_us)->latest('id')->first()
            ?? $this->buscarAbierto(self::normalizarCelular($ins->celular), $ins->email ? mb_strtolower($ins->email) : null, $ins->ci);

        if (! $lead) {
            $lead = $this->registrar([
                'nombre' => trim($ins->nombre.' '.$ins->appaterno), 'celular' => $ins->celular,
                'correo' => $ins->email, 'ci' => $ins->ci, 'programa_id' => $ins->id_mat,
            ], match ($ins->origen) { 'caja_admin' => 'caja', 'portal' => 'portal', default => 'portal' });
        }

        $lead->update([
            'id_us'       => $ins->id_us,
            'id_ins'      => $idIns,
            'programa_id' => $lead->programa_id ?: $ins->id_mat,
            'estado'      => $etapa,
            'cerrado_at'  => $etapa === 'inscrito' ? ($lead->cerrado_at ?? now()) : null,
            'motivo_perdida' => null,
        ]);

        return $lead->fresh();
    }

    /** Llamar después de una inscripción o un pago: nunca debe interrumpir el flujo que lo llama. */
    public static function alInscribir(?int $idIns): void
    {
        if (! $idIns) {
            return;
        }
        try {
            app(self::class)->sincronizarConInscripcion($idIns);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /** Entrada automática desde un formulario o canal: nunca debe interrumpir lo que la llama. */
    public static function alContactar(array $datos, string $origen): void
    {
        try {
            app(self::class)->registrar($datos, $origen);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /** Interesados abiertos que llevan más de 24 horas sin ningún contacto. */
    public function sinAtender()
    {
        $limite = now()->subHours(self::HORAS_SIN_ATENCION);

        return Lead::whereIn('estado', ['nuevo', 'contactado', 'interesado'])
            ->where(fn ($q) => $q->where('ultimo_contacto_at', '<', $limite)
                ->orWhere(fn ($q) => $q->whereNull('ultimo_contacto_at')->where('created_at', '<', $limite)));
    }

    public static function normalizarCelular(?string $celular): ?string
    {
        $digitos = preg_replace('/\D+/', '', (string) $celular);
        if ($digitos === '') {
            return null;
        }
        // Bolivia: se guarda sin el 591 para comparar igual "+591 7xx" y "7xx"
        if (strlen($digitos) === 11 && str_starts_with($digitos, '591')) {
            $digitos = substr($digitos, 3);
        }

        return $digitos;
    }

    public static function nombreOrigen(string $origen): string
    {
        return [
            'manual' => 'carga manual', 'excel' => 'Excel', 'portal' => 'el portal', 'contacto' => 'el formulario de contacto',
            'whatsapp' => 'WhatsApp', 'whatsapp_grupo' => 'un grupo de WhatsApp', 'meta' => 'un anuncio', 'caja' => 'Caja',
        ][$origen] ?? $origen;
    }

    private function buscarAbierto(?string $celular, ?string $correo, ?string $ci): ?Lead
    {
        return Lead::whereIn('estado', self::ABIERTAS)
            ->where(function ($q) use ($celular, $correo, $ci) {
                $q->whereRaw('1 = 0');
                if ($celular) $q->orWhere('celular', $celular);
                if ($correo)  $q->orWhereRaw('LOWER(correo) = ?', [$correo]);
                if ($ci)      $q->orWhere('ci', $ci);
            })
            ->latest('id')
            ->first();
    }

    private function estudianteDe(?string $celular, ?string $correo, ?string $ci): ?int
    {
        $q = DB::table('t_usuario')->where(function ($q) use ($celular, $correo, $ci) {
            $q->whereRaw('1 = 0');
            if ($ci)      $q->orWhere('ci', $ci);
            if ($celular) $q->orWhere('celular', $celular);
            if ($correo)  $q->orWhereRaw('LOWER(email) = ?', [$correo]);
        });
        $id = $q->min('id_us');

        return $id ? (int) $id : null;
    }
}
