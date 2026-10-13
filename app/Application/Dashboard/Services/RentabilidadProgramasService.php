<?php

namespace App\Application\Dashboard\Services;

use App\Application\Gastos\Services\GastosAutomaticosService;
use App\Application\Inscripciones\Services\InscripcionCuotasService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Ganancia de cada programa en un mes: lo cobrado a sus inscritos contra lo pagado a sus docentes,
 * su publicidad y los demás gastos asignados a él. Los gastos sin programa quedan como "Gastos generales".
 */
class RentabilidadProgramasService
{
    public function delMes(int $anio, int $mes): array
    {
        $inicio = Carbon::create($anio, $mes, 1)->toDateString();
        $fin = Carbon::create($anio, $mes, 1)->endOfMonth()->toDateString();

        // Una fila por versión: t_imparte puede repetir id_imp (registros por gestión)
        $versiones = DB::table('t_imparte')->selectRaw('id_imp, MIN(id_mat) as id_programa')->groupBy('id_imp');

        $ingresos = DB::table('t_pago as pg')
            ->join('t_inscripcion as i', 'i.id_ins', '=', 'pg.id_ins')
            ->joinSub($versiones, 'v', 'v.id_imp', '=', 'i.id_imp')
            ->where('pg.estado', 1)
            ->where(fn ($q) => $q->whereNull('pg.estado_verificacion')
                ->orWhere('pg.estado_verificacion', '!=', InscripcionCuotasService::COMPROBANTE_ESTUDIANTE))
            ->whereBetween('pg.fecha_deposito', [$inicio, $fin])
            ->groupBy('v.id_programa')
            ->selectRaw('v.id_programa, SUM(CAST(pg.monto_pagado AS DECIMAL(12,2))) as total')
            ->pluck('total', 'id_programa');

        $gastos = DB::table('gasto')
            ->whereNull('deleted_at')
            ->whereBetween('fecha', [$inicio, $fin])
            ->groupBy('id_programa', 'origen')
            ->selectRaw('id_programa, origen, SUM(monto) as total')
            ->get();

        $ids = $ingresos->keys()->merge($gastos->pluck('id_programa'))->filter()->unique()->values();
        $nombres = DB::table('t_programa')->whereIn('id_programa', $ids)->pluck('nombre_programa', 'id_programa');

        $filas = $ids->map(function ($id) use ($ingresos, $gastos, $nombres) {
            $suyos = $gastos->where('id_programa', $id);
            $docentes = (float) $suyos->where('origen', GastosAutomaticosService::HONORARIO)->sum('total');
            $publicidad = (float) $suyos->where('origen', GastosAutomaticosService::PUBLICIDAD)->sum('total');
            $otros = (float) $suyos->sum('total') - $docentes - $publicidad;
            $ingreso = (float) ($ingresos[$id] ?? 0);
            $gasto = $docentes + $publicidad + $otros;

            return [
                'id_programa' => (int) $id,
                'programa'    => $nombres[$id] ?? "Programa #{$id}",
                'ingresos'    => round($ingreso, 2),
                'docentes'    => round($docentes, 2),
                'publicidad'  => round($publicidad, 2),
                'otros'       => round($otros, 2),
                'gastos'      => round($gasto, 2),
                'ganancia'    => round($ingreso - $gasto, 2),
                'margen'      => $ingreso > 0 ? round(($ingreso - $gasto) / $ingreso * 100, 1) : null,
            ];
        })->sortByDesc('ingresos')->values()->all();

        return [
            'programas'        => $filas,
            'gastos_generales' => round((float) $gastos->whereNull('id_programa')->sum('total'), 2),
        ];
    }
}
