<?php

namespace App\Application\CampanasPublicidad\Services;

use App\Application\Inscripciones\Services\InscripcionCuotasService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use DomainException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Seguimiento de una campaña por marketing: registro diario (crecimiento del grupo,
 * flyers, otros gastos), resumen del periodo uniendo Facebook + registro + CRM,
 * e informe en PDF que queda en un historial que no se modifica.
 */
class SeguimientoCampanaService
{
    public function campana(int $id): object
    {
        $c = DB::table('campana_publicidad as cp')
            ->leftJoin('t_programa as p', 'p.id_programa', '=', 'cp.programa_id')
            ->whereNull('cp.deleted_at')->where('cp.id', $id)
            ->first(['cp.*', 'p.nombre_programa as programa_nombre']);
        if (! $c) {
            throw new DomainException('Campaña no encontrada.');
        }

        return $c;
    }

    public function registros(int $idCampana): array
    {
        return DB::table('campana_registro_diario as r')
            ->leftJoin('usuarios as u', 'u.id', '=', 'r.registrado_por')
            ->where('r.campana_publicidad_id', $idCampana)
            ->orderByDesc('r.fecha')
            ->get(['r.*', DB::raw(\App\Shared\Kernel\Support\SqlCompat::trimConcat("COALESCE(u.nombre,'')", "' '", "COALESCE(u.apellido,'')").' as registrado_por_nombre')])
            ->map(function ($r) {
                $r->flyer_url = $r->flyer_url ? '/storage/'.ltrim($r->flyer_url, '/') : null;
                $r->gasto_extra = (float) $r->gasto_extra;

                return $r;
            })->all();
    }

    /** Guarda el registro del día (uno por fecha: si ya existe, se actualiza). */
    public function guardarRegistro(int $idCampana, array $datos, ?UploadedFile $flyer, int $usuarioId): object
    {
        $this->campana($idCampana);
        $fecha = Carbon::parse($datos['fecha'])->toDateString();

        $existente = DB::table('campana_registro_diario')->where('campana_publicidad_id', $idCampana)->where('fecha', $fecha)->first();
        $ruta = $flyer ? $flyer->store("campanas/{$idCampana}/flyers", 'public') : ($existente->flyer_url ?? null);

        // Si no escribe el total, se calcula sumando los nuevos al último total registrado
        $total = $datos['total_grupo'] ?? null;
        if ($total === null || $total === '') {
            $anterior = (int) DB::table('campana_registro_diario')->where('campana_publicidad_id', $idCampana)
                ->where('fecha', '<', $fecha)->orderByDesc('fecha')->value('total_grupo');
            $total = $anterior + (int) ($datos['nuevos_grupo'] ?? 0);
        }

        DB::table('campana_registro_diario')->updateOrInsert(
            ['campana_publicidad_id' => $idCampana, 'fecha' => $fecha],
            [
                'nuevos_grupo'   => (int) ($datos['nuevos_grupo'] ?? 0),
                'total_grupo'    => (int) $total,
                'gasto_extra'    => round((float) ($datos['gasto_extra'] ?? 0), 2),
                'flyer_url'      => $ruta,
                'nota'           => $datos['nota'] ?? null,
                'registrado_por' => $usuarioId,
                'updated_at'     => now(),
                'created_at'     => $existente->created_at ?? now(),
            ]
        );
        if ($flyer && $existente?->flyer_url && $existente->flyer_url !== $ruta) {
            Storage::disk('public')->delete($existente->flyer_url);
        }

        $this->actualizarLeadsWhatsapp($idCampana);

        return DB::table('campana_registro_diario')->where('campana_publicidad_id', $idCampana)->where('fecha', $fecha)->first();
    }

    public function eliminarRegistro(int $idRegistro): void
    {
        $r = DB::table('campana_registro_diario')->where('id', $idRegistro)->first();
        if (! $r) {
            throw new DomainException('Registro no encontrado.');
        }
        DB::table('campana_registro_diario')->where('id', $idRegistro)->delete();
        if ($r->flyer_url) {
            Storage::disk('public')->delete($r->flyer_url);
        }
        $this->actualizarLeadsWhatsapp((int) $r->campana_publicidad_id);
    }

    /** Todo lo de la campaña en el periodo: Facebook, registro diario y resultados en el CRM. */
    public function resumen(int $idCampana, Carbon $desde, Carbon $hasta): array
    {
        $c = $this->campana($idCampana);
        $d = $desde->toDateString();
        $h = $hasta->toDateString();

        // Facebook guarda el gasto acumulado: el del periodo es la diferencia entre cortes
        $metricaHasta = DB::table('campana_metrica')->where('campana_publicidad_id', $idCampana)->where('fecha_corte', '<=', $h)->orderByDesc('fecha_corte')->first();
        $metricaAntes = DB::table('campana_metrica')->where('campana_publicidad_id', $idCampana)->where('fecha_corte', '<', $d)->orderByDesc('fecha_corte')->first();
        $gastoFacebook = max(0.0, (float) ($metricaHasta->gasto_periodo ?? 0) - (float) ($metricaAntes->gasto_periodo ?? 0));

        $registros = DB::table('campana_registro_diario')->where('campana_publicidad_id', $idCampana)
            ->whereBetween('fecha', [$d, $h])->orderBy('fecha')->get();
        $otrosGastos = (float) $registros->sum('gasto_extra')
            + (float) DB::table('gasto')->where('campana_publicidad_id', $idCampana)->where('origen', '!=', 'publicidad_meta')->whereNull('deleted_at')->whereBetween('fecha', [$d, $h])->sum('monto');
        $gastoTotal = round($gastoFacebook + $otrosGastos, 2);

        $nuevosGrupo = (int) $registros->sum('nuevos_grupo');
        $totalGrupo = (int) ($registros->last()->total_grupo ?? DB::table('campana_registro_diario')->where('campana_publicidad_id', $idCampana)->where('fecha', '<=', $h)->orderByDesc('fecha')->value('total_grupo') ?? 0);

        // CRM: interesados que vienen de esta campaña
        $leads = DB::table('leads')->where('campana_publicidad_id', $idCampana)->whereNull('deleted_at');
        $interesados = (clone $leads)->whereBetween('created_at', [$desde->copy()->startOfDay(), $hasta->copy()->endOfDay()])->count();
        $contactados = (clone $leads)->whereBetween('created_at', [$desde->copy()->startOfDay(), $hasta->copy()->endOfDay()])->whereNotNull('ultimo_contacto_at')->count();
        $inscritos = (clone $leads)->where('estado', 'inscrito')->whereBetween('cerrado_at', [$desde->copy()->startOfDay(), $hasta->copy()->endOfDay()])->count();
        $idsIns = (clone $leads)->whereNotNull('id_ins')->pluck('id_ins')->all();
        $cobrado = $idsIns ? round((float) DB::table('t_pago')->whereIn('id_ins', $idsIns)->where('estado', 1)
            ->where(fn ($q) => $q->whereNull('estado_verificacion')->orWhere('estado_verificacion', '!=', InscripcionCuotasService::COMPROBANTE_ESTUDIANTE))
            ->whereBetween('fecha_deposito', [$d, $h])->sum(DB::raw(\App\Shared\Kernel\Support\SqlCompat::castDecimal('monto_pagado'))), 2) : 0.0;

        return [
            'campana' => [
                'id' => (int) $c->id, 'nombre' => $c->nombre, 'plataforma' => $c->plataforma, 'estado' => $c->estado,
                'programa' => $c->programa_nombre, 'responsable' => $c->responsable,
                'fecha_inicio' => $c->fecha_inicio, 'fecha_fin' => $c->fecha_fin,
            ],
            'periodo' => ['desde' => $d, 'hasta' => $h],
            'facebook' => [
                'gasto'       => round($gastoFacebook, 2),
                'alcance'     => (int) ($metricaHasta->alcance ?? 0),
                'impresiones' => (int) ($metricaHasta->impresiones ?? 0),
                'clics'       => (int) ($metricaHasta->clics_enlace ?? 0),
                'resultados'  => (int) ($metricaHasta->resultados ?? 0),
                'actualizado' => $metricaHasta->fecha_corte ?? null,
            ],
            'grupo' => [
                'nuevos' => $nuevosGrupo,
                'total'  => $totalGrupo,
                'dias'   => $registros->map(fn ($r) => ['fecha' => $r->fecha, 'nuevos' => (int) $r->nuevos_grupo, 'total' => (int) $r->total_grupo])->values()->all(),
            ],
            'flyers' => $registros->whereNotNull('flyer_url')->map(fn ($r) => ['fecha' => $r->fecha, 'url' => '/storage/'.ltrim($r->flyer_url, '/'), 'ruta' => $r->flyer_url])->values()->all(),
            'notas' => $registros->whereNotNull('nota')->map(fn ($r) => ['fecha' => $r->fecha, 'nota' => $r->nota])->values()->all(),
            'gastos' => ['facebook' => round($gastoFacebook, 2), 'otros' => round($otrosGastos, 2), 'total' => $gastoTotal],
            'crm' => ['interesados' => $interesados, 'contactados' => $contactados, 'inscritos' => $inscritos, 'cobrado' => $cobrado],
            'costos' => [
                'por_persona_grupo' => $nuevosGrupo > 0 ? round($gastoTotal / $nuevosGrupo, 2) : null,
                'por_inscrito'      => $inscritos > 0 ? round($gastoTotal / $inscritos, 2) : null,
                'retorno'           => $gastoTotal > 0 ? round($cobrado / $gastoTotal, 2) : null,
            ],
        ];
    }

    /** Genera el PDF del periodo y lo guarda en el historial (no se modifica después). */
    public function generarInforme(int $idCampana, Carbon $desde, Carbon $hasta, ?object $usuario): object
    {
        $resumen = $this->resumen($idCampana, $desde, $hasta);

        // Las imágenes van incrustadas para que el PDF no dependa de rutas externas
        $flyers = collect($resumen['flyers'])->take(6)->map(function ($f) {
            $ruta = Storage::disk('public')->path($f['ruta']);
            $mime = @mime_content_type($ruta) ?: 'image/jpeg';

            return is_file($ruta) && str_starts_with($mime, 'image/')
                ? ['fecha' => $f['fecha'], 'src' => 'data:'.$mime.';base64,'.base64_encode((string) file_get_contents($ruta))]
                : null;
        })->filter()->values()->all();

        $autor = $usuario ? trim(($usuario->nombre ?? '').' '.($usuario->apellido ?? '')) : 'Sistema';
        $cargo = $usuario && method_exists($usuario, 'roles') ? $usuario->roles()->pluck('nombre')->join(', ') : '';
        $logo = resource_path('informes/cenefco-logo.png');
        $numero = str_pad((string) (DB::table('campana_informes')->where('campana_publicidad_id', $idCampana)->count() + 1), 3, '0', STR_PAD_LEFT);

        $pdf = Pdf::loadView('informes.campana', [
            'r'        => $resumen,
            'flyers'   => $flyers,
            'autor'    => $autor,
            'cargo'    => $cargo,
            'generado' => now(),
            'numero'   => "{$idCampana}-{$numero}",
            'titulo'   => $this->sinEmojis($resumen['campana']['nombre']),
            'logo'     => is_file($logo) ? 'data:image/png;base64,'.base64_encode((string) file_get_contents($logo)) : null,
        ])->setPaper('a4');

        $archivo = "campanas/{$idCampana}/informes/informe-{$resumen['periodo']['desde']}-a-{$resumen['periodo']['hasta']}-".now()->format('YmdHis').'.pdf';
        Storage::disk('public')->put($archivo, $pdf->output());

        $id = DB::table('campana_informes')->insertGetId([
            'campana_publicidad_id' => $idCampana,
            'desde'        => $resumen['periodo']['desde'],
            'hasta'        => $resumen['periodo']['hasta'],
            'archivo_url'  => $archivo,
            'resumen'      => json_encode(collect($resumen)->except(['flyers', 'notas'])->all()),
            'generado_por' => $usuario->id ?? null,
            'created_at'   => now(),
        ]);

        return $this->informes($idCampana, $id)[0];
    }

    /** La fuente del PDF no tiene emojis (🟢⛽...): saldrían como cuadros vacíos. */
    private function sinEmojis(string $texto): string
    {
        $limpio = preg_replace('/[\x{1F000}-\x{1FFFF}\x{2600}-\x{27BF}\x{2B00}-\x{2BFF}\x{FE00}-\x{FE0F}\x{200D}\x{20E3}]/u', '', $texto);

        return trim(preg_replace('/\s{2,}/u', ' ', $limpio ?? $texto)) ?: $texto;
    }

    public function informes(int $idCampana, ?int $soloId = null): array
    {
        return DB::table('campana_informes as i')
            ->leftJoin('usuarios as u', 'u.id', '=', 'i.generado_por')
            ->where('i.campana_publicidad_id', $idCampana)
            ->when($soloId, fn ($q) => $q->where('i.id', $soloId))
            ->orderByDesc('i.created_at')
            ->get(['i.id', 'i.desde', 'i.hasta', 'i.archivo_url', 'i.resumen', 'i.created_at', DB::raw(\App\Shared\Kernel\Support\SqlCompat::trimConcat("COALESCE(u.nombre,'')", "' '", "COALESCE(u.apellido,'')").' as generado_por')])
            ->map(function ($i) {
                $i->archivo_url = '/storage/'.ltrim($i->archivo_url, '/');
                $i->resumen = json_decode($i->resumen, true);

                return $i;
            })->all();
    }

    /** La columna "Leads WA" de la campaña refleja el último total del grupo registrado. */
    private function actualizarLeadsWhatsapp(int $idCampana): void
    {
        $total = DB::table('campana_registro_diario')->where('campana_publicidad_id', $idCampana)->orderByDesc('fecha')->value('total_grupo');
        DB::table('campana_publicidad')->where('id', $idCampana)->update(['leads_whatsapp' => $total, 'updated_at' => now()]);
    }
}
