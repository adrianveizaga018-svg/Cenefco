<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardGerencialController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $desde = $request->get('fecha_desde', now()->startOfMonth()->toDateString());
        $hasta = $request->get('fecha_hasta', now()->endOfMonth()->toDateString());

        // 1. Ingresos reales (pagos verificados)
        $ingresos = DB::table('t_pago')
            ->whereBetween('fecha_deposito', [$desde, $hasta])
            ->where('estado', 1)
            ->sum('monto_pagado');

        // 2. Inscripciones concretadas
        $inscripciones = DB::table('t_inscripcion')
            ->whereBetween('fecha_ins', [$desde, $hasta])
            ->where('estado', 1)
            ->count();

        // 3. Leads generados
        $leadsTotal = DB::table('leads')
            ->whereBetween('created_at', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->whereNull('deleted_at')
            ->count();

        // 4. Leads sin contactar (alerta)
        $leadsSinContactar = DB::table('leads')
            ->where('estado', 'nuevo')
            ->whereNull('deleted_at')
            ->count();

        // 5. Inversion en publicidad
        $inversionPublicidad = DB::table('campana_publicidad')
            ->where(function ($q) use ($desde, $hasta) {
                $q->whereBetween('fecha_inicio', [$desde, $hasta])
                  ->orWhereBetween('fecha_fin', [$desde, $hasta]);
            })
            ->whereNull('deleted_at')
            ->sum('presupuesto_bob');

        // 6. ROI
        $roi = $inversionPublicidad > 0
            ? round((($ingresos - $inversionPublicidad) / $inversionPublicidad) * 100, 1)
            : null;

        // 7. Tareas pendientes (alerta)
        $tareasPendientes = DB::table('tareas_academicas')
            ->where('estado', 'pendiente')
            ->count();

        // 8. Ingresos ultimos 6 meses (grafico barras) - SQLite compatible
        $ingresosPorMes = DB::table('t_pago')
            ->select(DB::raw("strftime('%Y-%m', fecha_deposito) as mes"), DB::raw('SUM(monto_pagado) as total'))
            ->where('estado', 1)
            ->whereRaw("fecha_deposito >= date('now', '-5 months', 'start of month')")
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        // 9. Leads por estado (grafico dona)
        $leadsPorEstado = DB::table('leads')
            ->select('estado', DB::raw('COUNT(*) as total'))
            ->whereNull('deleted_at')
            ->groupBy('estado')
            ->get();

        // 10. Top 5 programas con mas ingresos
        $topProgramas = DB::table('t_pago as p')
            ->join('t_inscripcion as i', 'p.id_ins', '=', 'i.id_ins')
            ->join('t_imparte as imp', 'i.id_imp', '=', 'imp.id_imp')
            ->join('t_programa as prog', 'imp.id_mat', '=', 'prog.id_programa')
            ->select('prog.nombre_programa', DB::raw('SUM(p.monto_pagado) as total_ingresos'), DB::raw('COUNT(DISTINCT i.id_ins) as total_inscritos'))
            ->where('p.estado', 1)
            ->whereBetween('p.fecha_deposito', [$desde, $hasta])
            ->groupBy('prog.id_programa', 'prog.nombre_programa')
            ->orderByDesc('total_ingresos')
            ->limit(5)
            ->get();

        // 11. Inscripciones por canal de venta
        $inscripcionesPorCanal = DB::table('t_inscripcion')
            ->select(DB::raw("COALESCE(canal_venta, 'Sin canal') as canal"), DB::raw('COUNT(*) as total'))
            ->whereBetween('fecha_ins', [$desde, $hasta])
            ->where('estado', 1)
            ->groupBy('canal')
            ->get();

        return response()->json([
            'periodo' => ['desde' => $desde, 'hasta' => $hasta],
            'kpis' => [
                'ingresos'             => round((float) $ingresos, 2),
                'inscripciones'        => $inscripciones,
                'leads_total'          => $leadsTotal,
                'leads_sin_contactar'  => $leadsSinContactar,
                'inversion_publicidad' => round((float) $inversionPublicidad, 2),
                'roi'                  => $roi,
                'tareas_pendientes'    => $tareasPendientes,
            ],
            'graficos' => [
                'ingresos_por_mes'         => $ingresosPorMes,
                'leads_por_estado'         => $leadsPorEstado,
                'top_programas'            => $topProgramas,
                'inscripciones_por_canal'  => $inscripcionesPorCanal,
            ],
        ]);
    }
}
