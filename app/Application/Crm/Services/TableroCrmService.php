<?php

namespace App\Application\Crm\Services;

use App\Application\Inscripciones\Services\InscripcionCuotasService;
use App\Infrastructure\CampanasLeads\Models\Lead;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Tablero de Dirección: cómo va cada vendedor en el periodo. Las ventas cuentan
 * lo cobrado (pagos válidos de sus inscripciones), con la misma regla que la deuda.
 */
class TableroCrmService
{
    public function __construct(private readonly CrmService $crm) {}

    /** @param int|null $soloVendedor un vendedor ve únicamente su propia fila */
    public function resumen(Carbon $desde, Carbon $hasta, ?int $soloVendedor = null): array
    {
        $hasta = $hasta->copy()->endOfDay();
        $vendedores = DB::table('vendedores')->whereNull('deleted_at')
            ->when($soloVendedor, fn ($q) => $q->where('id', $soloVendedor), fn ($q) => $q->where('activo', true))
            ->orderBy('nombre')->get(['id', 'nombre', 'apellido', 'meta_ventas']);

        $filas = $vendedores->map(function ($v) use ($desde, $hasta) {
            $asignados = Lead::where('vendedor_asignado_id', $v->id)->whereBetween('asignado_at', [$desde, $hasta]);
            $interesados = (clone $asignados)->count();
            $contactados = (clone $asignados)->whereNotNull('ultimo_contacto_at')->count();
            $inscritos = Lead::where('vendedor_asignado_id', $v->id)->where('estado', 'inscrito')->whereBetween('cerrado_at', [$desde, $hasta])->count();
            $cobrado = $this->cobradoDe(Lead::where('vendedor_asignado_id', $v->id)->whereNotNull('id_ins')->pluck('id_ins')->all(), $desde, $hasta);
            $meta = (float) ($v->meta_ventas ?? 0);

            return [
                'vendedor_id'         => (int) $v->id,
                'vendedor'            => trim($v->nombre.' '.$v->apellido),
                'interesados'         => $interesados,
                'contactados'         => $contactados,
                'inscritos'           => $inscritos,
                'conversion'          => $interesados ? round($inscritos / $interesados * 100, 1) : 0.0,
                'cobrado'             => $cobrado,
                'meta'                => $meta,
                'avance_meta'         => $meta > 0 ? round($cobrado / $meta * 100, 1) : null,
                'sin_atender'         => $this->crm->sinAtender()->where('vendedor_asignado_id', $v->id)->count(),
                'primera_respuesta_h' => $this->primeraRespuestaHoras($v->id, $desde, $hasta),
                'abiertos'            => Lead::where('vendedor_asignado_id', $v->id)->whereIn('estado', CrmService::ABIERTAS)->count(),
            ];
        })->values();

        $creados = Lead::whereBetween('created_at', [$desde, $hasta])
            ->when($soloVendedor, fn ($q) => $q->where('vendedor_asignado_id', $soloVendedor));
        $alcance = fn ($q) => $soloVendedor ? $q->where('vendedor_asignado_id', $soloVendedor) : $q;

        $respuestas = $filas->pluck('primera_respuesta_h')->filter(fn ($h) => $h !== null);

        return [
            'periodo' => ['desde' => $desde->toDateString(), 'hasta' => $hasta->toDateString()],
            'totales' => [
                'interesados'         => (clone $creados)->count(),
                'inscritos'           => $filas->sum('inscritos'),
                'conversion'          => ($n = (clone $creados)->count()) ? round($filas->sum('inscritos') / $n * 100, 1) : 0.0,
                'cobrado'             => round($filas->sum('cobrado'), 2),
                'sin_atender'         => $alcance($this->crm->sinAtender())->count(),
                'sin_asignar'         => $soloVendedor ? 0 : Lead::whereIn('estado', CrmService::ABIERTAS)->whereNull('vendedor_asignado_id')->count(),
                'primera_respuesta_h' => $respuestas->isNotEmpty() ? round($respuestas->avg(), 1) : null,
            ],
            'vendedores' => $filas,
            // Foto del embudo hoy: abiertos por etapa; cerrados del periodo
            'embudo' => collect(CrmService::ETAPAS)->mapWithKeys(fn ($e) => [$e => in_array($e, CrmService::ABIERTAS, true)
                ? $alcance(Lead::where('estado', $e))->count()
                : $alcance(Lead::where('estado', $e)->whereBetween('cerrado_at', [$desde, $hasta]))->count()]),
            'motivos_perdida' => $alcance(Lead::where('estado', 'perdido')->whereBetween('cerrado_at', [$desde, $hasta]))
                ->selectRaw('motivo_perdida as motivo, COUNT(*) as total')->groupBy('motivo_perdida')->orderByDesc('total')->limit(8)->get(),
            'por_origen' => (clone $creados)->selectRaw("origen, COUNT(*) as interesados, SUM(CASE WHEN estado = 'inscrito' THEN 1 ELSE 0 END) as inscritos")
                ->groupBy('origen')->orderByDesc('interesados')->get(),
        ];
    }

    /** Lo cobrado en el periodo sobre esas inscripciones (pagos válidos, sin comprobantes en revisión). */
    private function cobradoDe(array $idsIns, Carbon $desde, Carbon $hasta): float
    {
        if (! $idsIns) {
            return 0.0;
        }

        return round((float) DB::table('t_pago')
            ->whereIn('id_ins', $idsIns)
            ->where('estado', 1)
            ->where(fn ($q) => $q->whereNull('estado_verificacion')->orWhere('estado_verificacion', '!=', InscripcionCuotasService::COMPROBANTE_ESTUDIANTE))
            ->whereBetween('fecha_deposito', [$desde->toDateString(), $hasta->toDateString()])
            ->sum('monto_pagado'), 2);
    }

    /** Horas promedio entre que llega el interesado y su primer contacto registrado. */
    private function primeraRespuestaHoras(int $vendedorId, Carbon $desde, Carbon $hasta): ?float
    {
        $horas = Lead::where('vendedor_asignado_id', $vendedorId)
            ->whereBetween('created_at', [$desde, $hasta])
            ->get(['id', 'created_at'])
            ->map(function ($lead) {
                $primero = DB::table('lead_seguimientos')->where('lead_id', $lead->id)->whereNotNull('vendedor_id')->min('created_at');

                return $primero ? Carbon::parse($primero)->diffInMinutes($lead->created_at, true) / 60 : null;
            })
            ->filter(fn ($h) => $h !== null);

        return $horas->isNotEmpty() ? round($horas->avg(), 1) : null;
    }
}
