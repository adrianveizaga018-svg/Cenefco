<?php

namespace App\Application\Inscripciones\Services;

use App\Application\Usuarios\Services\PromocionEstudianteService;
use App\Enums\PrioridadEnum;
use App\Enums\TipoNotificacionEnum;
use App\Infrastructure\Usuarios\Models\User;
use App\Notifications\ComprobanteRevisadoNotification;
use App\Services\NotificacionService;
use App\Shared\Kernel\Support\BoletaValidator;
use App\Shared\Kernel\Support\SqlCompat;
use DomainException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

/**
 * Pago desde el panel del estudiante: sube su comprobante (queda "enviado_estudiante",
 * sin descontar deuda) y Caja lo aprueba o rechaza. Al aprobarse se recalcula el
 * cronograma, asciende a Estudiante y se le avisa por correo.
 */
class ComprobanteEstudianteService
{
    private const MAX_EN_REVISION = 3;

    public function __construct(
        private readonly AreaEstudianteService $area,
        private readonly InscripcionCuotasService $cuotas,
        private readonly PromocionEstudianteService $promocion,
        private readonly NotificacionService $notificaciones,
    ) {}

    /** Lo que el estudiante necesita para pagar una inscripción suya: saldo, próxima cuota, QR y cuentas. */
    public function infoPago(User $user, int $idIns): array
    {
        $ins = $this->inscripcionPropia($user, $idIns);
        $cronograma = $this->cuotas->sincronizar($idIns);

        $proxima = collect($cronograma->cuotas ?? [])->first(fn ($c) => $c->estado !== 'pagado');
        $plan    = $ins->id_plan ? DB::table('t_plan')->where('id_plan', $ins->id_plan)->first(['titulo', 'qr_image_url']) : null;

        return [
            'id_ins'   => $idIns,
            'programa' => $ins->programa,
            // Preinscripción del portal: aún no eligió plan de pago
            'requiere_plan' => ! $ins->id_plan,
            'planes'   => $ins->id_plan ? [] : $this->area->planesDe((int) $ins->id_imp, (int) $ins->id_mat)->values()->all(),
            'plan'     => $plan?->titulo,
            'qr_url'   => $plan?->qr_image_url ? '/storage/'.ltrim($plan->qr_image_url, '/') : null,
            'saldo'    => (float) ($cronograma->pendiente ?? 0),
            'proxima'  => $proxima ? [
                'descripcion' => $proxima->descripcion,
                'saldo'       => round((float) $proxima->monto_a_pagar - (float) $proxima->monto_pagado, 2),
                'vence'       => $proxima->fecha_vencimiento,
            ] : null,
            'bancos'   => $this->bancos(),
            'en_revision' => DB::table('t_pago')->where('id_ins', $idIns)->where('estado', 1)
                ->where('estado_verificacion', InscripcionCuotasService::COMPROBANTE_ESTUDIANTE)
                ->count(),
        ];
    }

    public function bancos(): array
    {
        return DB::table('tipos_banco')->where('activo', true)->orderBy('orden')
            ->get(['id', 'nombre', 'numero_cuenta', 'titular'])->all();
    }

    /** Comprobante para una inscripción que ya tiene. */
    public function enviar(User $user, int $idIns, array $datos, UploadedFile $archivo): int
    {
        $ins = $this->inscripcionPropia($user, $idIns);

        if (! $ins->id_plan) {
            if (empty($datos['id_plan'])) {
                throw new DomainException('Elige primero tu plan de pago.');
            }
            $this->asignarPlan($user, $idIns, (int) $datos['id_plan']);
        }

        $saldo = (float) ($this->cuotas->sincronizar($idIns)->pendiente ?? 0);
        if ($saldo <= 0) {
            throw new DomainException('Esta inscripción no tiene saldo pendiente.');
        }
        if ((float) $datos['monto'] > $saldo + 0.01) {
            throw new DomainException('El monto supera tu saldo pendiente (Bs. '.number_format($saldo, 2).').');
        }

        $this->validarEnvio($idIns, $datos);
        $ruta = $archivo->store('pagos/estudiantes', 'public');

        $idPago = DB::transaction(fn () => $this->registrarPago($user, (int) $ins->id_us, $idIns, $datos, $ruta));
        $this->avisarCaja($ins->programa, (float) $datos['monto']);

        return $idPago;
    }

    /**
     * Elige el plan de una preinscripción del portal (que nace sin plan) y crea su cronograma.
     * Con un plan gratuito la inscripción queda lista y asciende a Estudiante.
     *
     * @return array{gratuito: bool, saldo: float}
     */
    public function asignarPlan(User $user, int $idIns, int $idPlan): array
    {
        $ins = $this->inscripcionPropia($user, $idIns);
        if ($ins->id_plan) {
            throw new DomainException('Esta inscripción ya tiene un plan de pago. Para cambiarlo comunícate con CENEFCO.');
        }

        $plan = $this->area->planesDe((int) $ins->id_imp, (int) $ins->id_mat)->firstWhere('id_plan', $idPlan);
        if (! $plan) {
            throw new DomainException('Ese plan de pago no está disponible para este programa.');
        }

        DB::transaction(function () use ($idIns, $idPlan) {
            $actualizadas = DB::table('t_inscripcion')->where('id_ins', $idIns)->whereNull('id_plan')->update(['id_plan' => $idPlan]);
            if (! $actualizadas) {
                throw new DomainException('Esta inscripción ya tiene un plan de pago.');
            }
            $this->cuotas->generarCuotasParaInscripcion($idIns, $idPlan);
        });

        $gratuito = (bool) $plan['gratuito'] && \App\Shared\Kernel\Support\PlanGratuito::es($idPlan);
        if ($gratuito) {
            $this->promocion->promoverPorIdUs((int) $ins->id_us);
        }

        return ['gratuito' => $gratuito, 'saldo' => (float) ($this->cuotas->sincronizar($idIns)->pendiente ?? 0)];
    }

    /** Inscripción a un programa de pago con el comprobante del primer pago. */
    public function inscribirConComprobante(User $user, int $idImp, int $idPlan, array $datos, UploadedFile $archivo): array
    {
        [$imp, $plan] = $this->area->validarOferta($user, $idImp, $idPlan);
        if ($plan['gratuito']) {
            throw new DomainException('Este plan es gratuito: usa "Inscribirme gratis".');
        }

        $this->validarEnvio(null, $datos);
        $ruta = $archivo->store('pagos/estudiantes', 'public');

        $idIns = DB::transaction(function () use ($user, $imp, $idPlan, $datos, $ruta) {
            $idIns = $this->area->crearInscripcion($user, $imp, $idPlan);
            $idUs  = (int) DB::table('t_inscripcion')->where('id_ins', $idIns)->value('id_us');
            $this->registrarPago($user, $idUs, $idIns, $datos, $ruta);

            return $idIns;
        });

        $this->avisarCaja($imp->nombre_programa, (float) $datos['monto'], nueva: true);

        return ['id_ins' => $idIns, 'programa' => $imp->nombre_programa];
    }

    /** Bandeja de Caja. */
    public function listar(string $estado): array
    {
        $q = DB::table('t_pago as p')
            ->join('t_inscripcion as i', 'i.id_ins', '=', 'p.id_ins')
            ->leftJoin('t_usuario as u', function ($j) {
                $j->on('u.id_us', '=', 'i.id_us')
                  ->whereRaw('u.id_us_reg = (SELECT MIN(u2.id_us_reg) FROM t_usuario u2 WHERE u2.id_us = u.id_us)');
            })
            ->leftJoin('t_imparte as imp', 'imp.id_imp', '=', 'i.id_imp')
            ->leftJoin('t_programa as prog', 'prog.id_programa', '=', 'imp.id_mat')
            ->leftJoin('tipos_banco as tb', 'tb.id', '=', 'p.tipo_banco_id')
            ->where('p.nota_verificacion', 'like', 'Comprobante del estudiante%');

        match ($estado) {
            'aprobados'  => $q->where('p.estado', 1)->where('p.estado_verificacion', 'verificado'),
            'rechazados' => $q->where('p.estado_verificacion', 'rechazado'),
            'todos'      => null,
            default      => $q->where('p.estado', 1)->where('p.estado_verificacion', InscripcionCuotasService::COMPROBANTE_ESTUDIANTE),
        };

        $data = $q->orderByDesc('p.fecha_reg')->limit(200)->get([
            'p.id_pago', 'p.id_ins', 'i.id_us', 'p.monto_pagado as monto', 'p.fecha_deposito', 'p.metodo_pago',
            'p.nro_boleta_bancaria as nro_boleta', 'p.comprobante_url', 'p.estado_verificacion', 'p.nota_verificacion',
            'p.fecha_reg', 'tb.nombre as banco', 'u.ci', 'u.celular',
            DB::raw(SqlCompat::trimConcat("COALESCE(u.nombre,'')", "' '", "COALESCE(u.appaterno,'')", "' '", "COALESCE(u.apmaterno,'')").' as estudiante'),
            DB::raw('COALESCE(prog.nombre_programa, imp.titulo_personalizado) as programa'),
            DB::raw("CASE WHEN i.origen = 'panel_estudiante' AND (SELECT COUNT(*) FROM t_pago p2 WHERE p2.id_ins = p.id_ins AND p2.id_pago < p.id_pago) = 0 THEN 1 ELSE 0 END as es_inscripcion"),
        ])->map(function ($p) {
            $p->monto           = (float) $p->monto;
            $p->es_inscripcion  = (bool) $p->es_inscripcion;
            $p->comprobante_url = $p->comprobante_url ? '/storage/'.ltrim($p->comprobante_url, '/') : null;
            $p->saldo_inscripcion = (float) DB::table('t_inscripcion_cuotas')->where('id_ins', $p->id_ins)
                ->sum(DB::raw('monto_a_pagar - monto_pagado'));

            return $p;
        })->all();

        return ['data' => $data, 'pendientes' => $this->pendientes()];
    }

    public function pendientes(): int
    {
        return DB::table('t_pago')->where('estado', 1)
            ->where('estado_verificacion', InscripcionCuotasService::COMPROBANTE_ESTUDIANTE)->count();
    }

    public function aprobar(int $idPago, User $cajero): array
    {
        $pago = $this->pagoEnRevision($idPago);

        DB::transaction(function () use ($pago, $cajero) {
            DB::table('t_pago')->where('id_pago', $pago->id_pago)->update([
                'estado_verificacion' => 'verificado',
                'id_us_cajero'        => $cajero->id,
                'nota_verificacion'   => 'Comprobante del estudiante aprobado por '.trim($cajero->nombre.' '.$cajero->apellido),
            ]);
            $this->cuotas->sincronizar((int) $pago->id_ins);
        });

        $this->promocion->promoverPorIdUs((int) $pago->id_us);
        $saldo = (float) ($this->cuotas->sincronizar((int) $pago->id_ins)->pendiente ?? 0);
        $this->avisarEstudiante($pago, true, null, $saldo);

        return ['saldo' => $saldo];
    }

    public function rechazar(int $idPago, User $cajero, string $motivo): void
    {
        $pago = $this->pagoEnRevision($idPago);

        DB::table('t_pago')->where('id_pago', $pago->id_pago)->update([
            'estado'              => 0,
            'estado_verificacion' => 'rechazado',
            'id_us_cajero'        => $cajero->id,
            'nota_verificacion'   => 'Comprobante del estudiante rechazado: '.$motivo,
        ]);

        $this->avisarEstudiante($pago, false, $motivo, 0);
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function inscripcionPropia(User $user, int $idIns): object
    {
        $idUs = $this->area->idUs($user);
        $ins = $idUs ? DB::table('t_inscripcion as i')
            ->leftJoin('t_imparte as imp', 'imp.id_imp', '=', 'i.id_imp')
            ->leftJoin('t_programa as prog', 'prog.id_programa', '=', 'imp.id_mat')
            ->where('i.id_ins', $idIns)->where('i.id_us', $idUs)->where('i.estado', 1)
            ->first(['i.id_ins', 'i.id_us', 'i.id_plan', 'i.id_imp', 'imp.id_mat', DB::raw('COALESCE(prog.nombre_programa, imp.titulo_personalizado) as programa')])
            : null;

        if (! $ins) {
            throw new DomainException('No encontramos esa inscripción en tu cuenta.');
        }

        return $ins;
    }

    private function validarEnvio(?int $idIns, array $datos): void
    {
        if ($idIns && DB::table('t_pago')->where('id_ins', $idIns)->where('estado', 1)
                ->where('estado_verificacion', InscripcionCuotasService::COMPROBANTE_ESTUDIANTE)->count() >= self::MAX_EN_REVISION) {
            throw new DomainException('Ya tienes comprobantes esperando revisión. Espera a que Caja los revise.');
        }

        $boleta = BoletaValidator::verificar($datos['nro_boleta'] ?? null);
        if ($boleta['duplicada']) {
            throw new DomainException('Ese número de comprobante ya fue registrado. Revisa el número o comunícate con CENEFCO.');
        }
    }

    private function registrarPago(User $user, int $idUs, int $idIns, array $datos, string $ruta): int
    {
        $idPago = (int) (DB::table('t_pago')->max('id_pago') ?? 0) + 1;

        DB::table('t_pago')->insert([
            'id_pago'             => $idPago,
            'id_us_reg'           => $user->id,
            'id_us'               => $idUs,
            'id_ins'              => $idIns,
            'monto_pagado'        => round((float) $datos['monto'], 2),
            'nro_boleta_bancaria' => $datos['nro_boleta'] ?? null,
            'fecha_deposito'      => $datos['fecha_deposito'],
            'metodo_pago'         => $datos['metodo_pago'],
            'tipo_banco_id'       => $datos['tipo_banco_id'] ?? null,
            'estado'              => 1,
            'estado_verificacion' => InscripcionCuotasService::COMPROBANTE_ESTUDIANTE,
            'nota_verificacion'   => 'Comprobante del estudiante subido desde su panel',
            'fecha_reg'           => now(),
            'pago_extra'          => 0,
            'comprobante_url'     => $ruta,
        ]);

        return $idPago;
    }

    private function pagoEnRevision(int $idPago): object
    {
        $pago = DB::table('t_pago')->where('id_pago', $idPago)->first();
        if (! $pago || (int) $pago->estado !== 1 || $pago->estado_verificacion !== InscripcionCuotasService::COMPROBANTE_ESTUDIANTE) {
            throw new DomainException('Este comprobante ya fue revisado o no existe.');
        }

        return $pago;
    }

    private function avisarCaja(?string $programa, float $monto, bool $nueva = false): void
    {
        try {
            $this->notificaciones->enviarAPermiso(
                permiso:   'inscripciones.crear',
                tipo:      TipoNotificacionEnum::PAGO_REGISTRADO,
                titulo:    $nueva ? 'Nueva inscripción online por verificar' : 'Comprobante de pago por verificar',
                mensaje:   'Un estudiante subió un comprobante de Bs. '.number_format($monto, 2).' para '.($programa ?: 'su programa').'.',
                prioridad: PrioridadEnum::ALTA,
                urlAccion: '/caja/comprobantes',
                icono:     'lucideReceipt',
                color:     '#f59e0b',
            );
        } catch (\Throwable $e) {
            report($e); // el aviso al personal nunca debe impedir que el estudiante envíe su pago
        }
    }

    private function avisarEstudiante(object $pago, bool $aprobado, ?string $motivo, float $saldo): void
    {
        $est = DB::table('t_usuario')->where('id_us', $pago->id_us)->orderBy('id_us_reg')->first(['nombre', 'email']);
        if (! $est?->email) {
            return;
        }

        $programa = DB::table('t_inscripcion as i')
            ->leftJoin('t_imparte as imp', 'imp.id_imp', '=', 'i.id_imp')
            ->leftJoin('t_programa as prog', 'prog.id_programa', '=', 'imp.id_mat')
            ->where('i.id_ins', $pago->id_ins)
            ->value(DB::raw('COALESCE(prog.nombre_programa, imp.titulo_personalizado)'));

        try {
            Notification::route('mail', $est->email)->notify(new ComprobanteRevisadoNotification(
                nombre: (string) $est->nombre,
                programa: (string) $programa,
                monto: (float) $pago->monto_pagado,
                aprobado: $aprobado,
                motivo: $motivo,
                saldoPendiente: $saldo,
            ));
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
