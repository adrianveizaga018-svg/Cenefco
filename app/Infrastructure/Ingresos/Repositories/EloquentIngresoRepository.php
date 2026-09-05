<?php

namespace App\Infrastructure\Ingresos\Repositories;

use App\Domain\Ingresos\Contracts\IngresoRepositoryInterface;
use App\Shared\Kernel\DTOs\PaginationDTO;
use App\Shared\Kernel\Support\SqlCompat;
use Illuminate\Support\Facades\DB;

class EloquentIngresoRepository implements IngresoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?string $desde, ?string $hasta): array
    {
        $q = DB::table('t_pago as p')
            ->leftJoin('t_usuario as u', function ($j) {

                $j->on('p.id_us', '=', 'u.id_us')
                  ->whereRaw("u.id_us_reg = COALESCE(
                        (SELECT MIN(u2.id_us_reg) FROM t_usuario u2 WHERE u2.id_us = u.id_us AND u2.tipoestudiante = '2'),
                        (SELECT MIN(u3.id_us_reg) FROM t_usuario u3 WHERE u3.id_us = u.id_us)
                    )");
            })
            ->leftJoin('t_fechapago as fp', function ($j) {

                $j->on('p.id_fechapago', '=', 'fp.id_fechapago')
                  ->whereRaw('fp.id_us_reg = (SELECT MIN(f2.id_us_reg) FROM t_fechapago f2 WHERE f2.id_fechapago = fp.id_fechapago)');
            })
            ->leftJoin('t_plan as pl', function ($j) {

                $j->on('fp.id_plan', '=', 'pl.id_plan')
                  ->whereRaw('pl.id_us_reg = (SELECT MIN(p2.id_us_reg) FROM t_plan p2 WHERE p2.id_plan = pl.id_plan)');
            })
            ->select([
                'p.id_pago',
                'p.id_us',
                'p.monto_pagado',
                'p.nro_boleta_bancaria',
                'p.fecha_deposito',
                'p.observacion_pago',
                'p.estado',
                DB::raw("TRIM(CONCAT(COALESCE(u.nombre,''), ' ', COALESCE(u.appaterno,''))) as estudiante_nombre"),
                'u.ci as estudiante_ci',
                'fp.nro_pago as cuota_nro',
                'fp.tipo_tramite',
                'pl.titulo as plan_titulo',
            ])
            ->where('p.estado', 1);

        if ($desde) {
            $q->whereDate('p.fecha_deposito', '>=', $desde);
        }
        if ($hasta) {
            $q->whereDate('p.fecha_deposito', '<=', $hasta);
        }

        $total        = $q->count();
        $totalPeriodo = (float) ((clone $q)->cloneWithout(['columns'])->selectRaw('SUM(CAST(p.monto_pagado AS DECIMAL(12,2))) as agg')->value('agg') ?? 0);

        $data = $q->orderByDesc('p.fecha_deposito')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get()
            ->all();

        return ['data' => $data, 'total' => $total, 'total_periodo' => $totalPeriodo];
    }

    public function resumen(): array
    {
        $ahora     = now();
        $inicioMes = $ahora->copy()->startOfMonth()->toDateString();
        $finMes    = $ahora->copy()->endOfMonth()->toDateString();
        $inicioAno = $ahora->copy()->startOfYear()->toDateString();

        $base = DB::table('t_pago')->where('estado', 1);

        $totalGeneral = (float) ((clone $base)->selectRaw('SUM(CAST(monto_pagado AS DECIMAL(12,2))) as agg')->value('agg') ?? 0);
        $totalMes     = (float) ((clone $base)->whereBetween('fecha_deposito', [$inicioMes, $finMes])->selectRaw('SUM(CAST(monto_pagado AS DECIMAL(12,2))) as agg')->value('agg') ?? 0);
        $totalAno     = (float) ((clone $base)->whereBetween('fecha_deposito', [$inicioAno, $finMes])->selectRaw('SUM(CAST(monto_pagado AS DECIMAL(12,2))) as agg')->value('agg') ?? 0);
        $nPagos       = (clone $base)->count();
        $nPagosMes    = (clone $base)->whereBetween('fecha_deposito', [$inicioMes, $finMes])->count();
        $promedio     = $nPagos > 0 ? round($totalGeneral / $nPagos, 2) : 0;

        $mesExpr = SqlCompat::dateFormat('fecha_deposito', '%Y-%m');
        $meses = DB::table('t_pago')
            ->where('estado', 1)
            ->whereNotNull('fecha_deposito')
            ->selectRaw("{$mesExpr} as mes, COUNT(*) as n_pagos, SUM(CAST(monto_pagado AS DECIMAL(12,2))) as total")
            ->groupByRaw($mesExpr)
            ->orderByDesc('mes')
            ->limit(12)
            ->get()
            ->reverse()
            ->values()
            ->all();

        return [
            'total_general' => $totalGeneral,
            'total_mes'     => $totalMes,
            'total_ano'     => $totalAno,
            'n_pagos'       => $nPagos,
            'n_pagos_mes'   => $nPagosMes,
            'promedio'      => $promedio,
            'meses'         => $meses,
        ];
    }
}
