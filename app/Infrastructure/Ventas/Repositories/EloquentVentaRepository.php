<?php

namespace App\Infrastructure\Ventas\Repositories;

use App\Application\Pagos\Services\PagoCalculadorService;
use App\Domain\Ventas\Contracts\VentaRepositoryInterface;
use App\Domain\Ventas\Exceptions\VentaNotFoundException;
use App\Shared\Kernel\DTOs\PaginationDTO;
use App\Shared\Kernel\Support\SqlCompat;
use Illuminate\Support\Facades\DB;

class EloquentVentaRepository implements VentaRepositoryInterface
{
    public function __construct(
        private readonly PagoCalculadorService $calculador,
    ) {}

    private function baseQuery(): \Illuminate\Database\Query\Builder
    {
        $planSub = DB::table('t_fechapago')
            ->select('id_plan', DB::raw('SUM(CAST(monto_a_pagar AS DECIMAL(12,2))) as total_a_pagar'))
            ->where('estado', 1)
            ->groupBy('id_plan');

        return DB::table('t_inscripcion as ins')
            ->leftJoin('t_usuario as u', function ($j) {
                $j->on('ins.id_us', '=', 'u.id_us')
                  ->whereRaw('u.id_us_reg = (SELECT MIN(u2.id_us_reg) FROM t_usuario u2 WHERE u2.id_us = u.id_us)');
            })
            ->leftJoin('t_imparte as imp', function ($j) {
                $j->on('ins.id_imp', '=', 'imp.id_imp')
                  ->whereRaw('imp.id_us_reg = (SELECT MIN(i2.id_us_reg) FROM t_imparte i2 WHERE i2.id_imp = imp.id_imp)');
            })
            ->leftJoin('t_programa as prog', function ($j) {
                $j->on('prog.id_imp', '=', 'ins.id_imp')
                  ->whereRaw('prog.id_us_reg = (SELECT MIN(p2.id_us_reg) FROM t_programa p2 WHERE p2.id_imp = prog.id_imp)');
            })
            ->leftJoin('t_materia as mat', function ($j) {
                $j->on('mat.id_mat', '=', 'imp.id_mat')
                  ->whereRaw('mat.id_us_reg = (SELECT MIN(m2.id_us_reg) FROM t_materia m2 WHERE m2.id_mat = mat.id_mat)');
            })
            ->leftJoinSub($planSub, 'plan_agg', 'plan_agg.id_plan', '=', 'ins.id_plan')
            ->leftJoin('t_usuario as vend', function ($j) {
                $j->on('ins.id_vendedor', '=', 'vend.id_us')
                  ->whereRaw('vend.id_us_reg = (SELECT MIN(v2.id_us_reg) FROM t_usuario v2 WHERE v2.id_us = vend.id_us)');
            })
            ->select([
                'ins.id_ins',
                'ins.id_us',
                'ins.id_imp',
                'ins.id_plan',
                'ins.periodo',
                'ins.gestion',
                'ins.fecha_ins',
                'ins.estado',
                'ins.fecha_reg',
                'ins.canal_venta',
                'ins.id_vendedor',
                DB::raw("TRIM(CONCAT(COALESCE(u.nombre,''), ' ', COALESCE(u.appaterno,''), ' ', COALESCE(u.apmaterno,''))) as estudiante_nombre"),
                'u.ci   as estudiante_ci',
                'u.celular as estudiante_celular',
                'u.email   as estudiante_email',
                'prog.id_programa',
                DB::raw('COALESCE(prog.nombre_programa, imp.titulo_personalizado, mat.nombre, mat.nombremat) as nombre_programa'),
                'prog.slug as programa_slug',
                DB::raw("TRIM(CONCAT(COALESCE(vend.nombre,''), ' ', COALESCE(vend.appaterno,''))) as vendedor_nombre"),
                DB::raw('COALESCE(plan_agg.total_a_pagar, 0) as total_a_pagar'),
                DB::raw($this->totalPagadoExpr() . ' as total_pagado'),
                DB::raw('(
                    SELECT COUNT(*)
                    FROM t_pago p3
                    WHERE p3.id_us = ins.id_us
                      AND p3.estado = 1
                      AND (
                          p3.id_ins = ins.id_ins
                          OR (p3.id_ins IS NULL AND (
                              p3.id_fechapago IN (
                                  SELECT fp3.id_fechapago FROM t_fechapago fp3 WHERE fp3.id_plan = ins.id_plan
                              )
                              OR p3.pago_extra = 1
                          ))
                      )
                ) as nro_pagos'),
            ]);
    }

    private function totalPagadoExpr(): string
    {

        return 'COALESCE((
                    SELECT SUM(CAST(p2.monto_pagado AS DECIMAL(12,2)))
                    FROM t_pago p2
                    WHERE p2.id_us = ins.id_us
                      AND p2.estado = 1
                      AND (
                          p2.id_ins = ins.id_ins
                          OR (p2.id_ins IS NULL AND (
                              p2.id_fechapago IN (
                                  SELECT fp2.id_fechapago FROM t_fechapago fp2 WHERE fp2.id_plan = ins.id_plan
                              )
                              OR p2.pago_extra = 1
                          ))
                      )
                ), 0)';
    }

    private function withEstadoPago(object $row): object
    {
        $row->total_a_pagar   = (float) $row->total_a_pagar;
        $row->total_pagado    = (float) $row->total_pagado;
        $row->saldo_pendiente = max(0.0, $row->total_a_pagar - $row->total_pagado);

        $row->estado_pago = match (true) {
            $row->total_a_pagar > 0 && $row->total_pagado >= $row->total_a_pagar => 'pagado',
            $row->total_pagado > 0  => 'parcial',
            default                 => 'pendiente',
        };

        return $row;
    }

    public function paginate(PaginationDTO $pagination, array $filters = []): array
    {
        $q = $this->baseQuery();

        if (! empty($filters['query'])) {
            $search = $filters['query'];
            $q->where(function ($sq) use ($search) {
                $sq->where('u.nombre', 'like', "%{$search}%")
                   ->orWhere('u.appaterno', 'like', "%{$search}%")
                   ->orWhere('u.ci', 'like', "%{$search}%")
                   ->orWhere('prog.nombre_programa', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['periodo'])) {
            $q->where('ins.periodo', $filters['periodo']);
        }

        if (! empty($filters['gestion'])) {
            $q->where('ins.gestion', $filters['gestion']);
        }

        if (! empty($filters['canal_venta'])) {
            $q->where('ins.canal_venta', $filters['canal_venta']);
        }

        if (! empty($filters['id_vendedor'])) {
            $q->where('ins.id_vendedor', $filters['id_vendedor']);
        }

        if (empty($filters['conInactivos'])) {
            $q->where('ins.estado', 1);
        }

        $wrapped = DB::table(DB::raw("({$q->toSql()}) as sub"))->mergeBindings($q);

        if (! empty($filters['estado_pago'])) {
            match ($filters['estado_pago']) {
                'pagado'    => $wrapped->whereRaw('sub.total_pagado >= sub.total_a_pagar AND sub.total_a_pagar > 0'),
                'parcial'   => $wrapped->whereRaw('sub.total_pagado > 0 AND sub.total_pagado < sub.total_a_pagar'),
                'pendiente' => $wrapped->whereRaw('sub.total_pagado = 0'),
                default     => null,
            };
        }

        $total = (clone $wrapped)->count();

        $data = $wrapped
            ->orderByDesc('sub.fecha_reg')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get()
            ->map(fn ($r) => $this->withEstadoPago($r))
            ->all();

        return ['data' => $data, 'total' => $total];
    }

    public function findById(int $id): object
    {
        $venta = $this->baseQuery()->where('ins.id_ins', $id)->first();
        if (! $venta) {
            throw new VentaNotFoundException($id);
        }

        $venta = $this->withEstadoPago($venta);

        $pagos = DB::table('t_pago as p')
            ->leftJoin('t_fechapago as fp', 'p.id_fechapago', '=', 'fp.id_fechapago')
            ->where('p.id_us', $venta->id_us)
            ->where('p.estado', 1)
            ->where(function ($q) use ($venta) {

                $q->where('p.id_ins', $venta->id_ins)
                  ->orWhere(function ($qq) use ($venta) {
                      $qq->whereNull('p.id_ins');
                      if ($venta->id_plan) {
                          $qq->where(function ($qqq) use ($venta) {
                              $qqq->whereIn('p.id_fechapago', function ($sub) use ($venta) {
                                  $sub->select('id_fechapago')
                                      ->from('t_fechapago')
                                      ->where('id_plan', $venta->id_plan);
                              })->orWhere('p.pago_extra', 1);
                          });
                      } else {
                          $qq->where('p.pago_extra', 1);
                      }
                  });
            })
            ->select([
                'p.id_pago', 'p.monto_pagado', 'p.nro_boleta_bancaria',
                'p.fecha_deposito', 'p.nro_nit', 'p.nombre_nit',
                'p.observacion_pago', 'p.pago_extra', 'p.fecha_reg',
                'p.metodo_pago', 'p.id_us_cajero',
                'fp.nro_pago', 'fp.monto_a_pagar',
            ])
            ->orderBy('p.fecha_deposito')
            ->get();

        $venta->pagos  = $pagos;
        $venta->resumen = $this->calculador->calcular(
            $venta->id_plan ? (int) $venta->id_plan : null,
            collect($pagos)
        );

        return $venta;
    }

    public function reporte(array $filters = []): array
    {
        $aplicarFiltros = function (\Illuminate\Database\Query\Builder $q) use ($filters) {
            $q->where('ins.estado', 1);

            if (! empty($filters['query'])) {
                $search = $filters['query'];
                $q->where(function ($sq) use ($search) {
                    $sq->where('u.nombre', 'like', "%{$search}%")
                       ->orWhere('u.appaterno', 'like', "%{$search}%")
                       ->orWhere('prog.nombre_programa', 'like', "%{$search}%");
                });
            }

            if (! empty($filters['periodo'])) {
                $q->where('ins.periodo', $filters['periodo']);
            }

            if (! empty($filters['gestion'])) {
                $q->where('ins.gestion', $filters['gestion']);
            }
        };

        $qListado = $this->baseQuery();
        $aplicarFiltros($qListado);
        $data = $qListado->orderByDesc('ins.fecha_reg')
            ->limit(2000)
            ->get()
            ->map(fn ($r) => $this->withEstadoPago($r));

        $qTotales = $this->baseQuery();
        $aplicarFiltros($qTotales);
        $wrapped = DB::table(DB::raw("({$qTotales->toSql()}) as sub"))->mergeBindings($qTotales);

        $agregado = $wrapped->selectRaw('
                COUNT(*) as total_registros,
                COALESCE(SUM(sub.total_a_pagar), 0) as total_a_pagar,
                COALESCE(SUM(sub.total_pagado), 0) as total_pagado,
                COALESCE(SUM(GREATEST(sub.total_a_pagar - sub.total_pagado, 0)), 0) as saldo_pendiente,
                SUM(CASE WHEN sub.total_a_pagar > 0 AND sub.total_pagado >= sub.total_a_pagar THEN 1 ELSE 0 END) as pagados,
                SUM(CASE WHEN sub.total_pagado > 0 AND NOT (sub.total_a_pagar > 0 AND sub.total_pagado >= sub.total_a_pagar) THEN 1 ELSE 0 END) as parciales,
                SUM(CASE WHEN sub.total_pagado <= 0 THEN 1 ELSE 0 END) as pendientes
            ')
            ->first();

        $totales = [
            'total_registros' => (int) $agregado->total_registros,
            'total_a_pagar'   => (float) $agregado->total_a_pagar,
            'total_pagado'    => (float) $agregado->total_pagado,
            'saldo_pendiente' => (float) $agregado->saldo_pendiente,
            'pagados'         => (int) $agregado->pagados,
            'parciales'       => (int) $agregado->parciales,
            'pendientes'      => (int) $agregado->pendientes,
        ];

        return ['data' => $data->all(), 'totales' => $totales];
    }

    private function agregarPorColumna(array $filtros, string $expresionAgrupacion, string $aliasAgrupacion): \Illuminate\Support\Collection
    {
        $q = $this->baseQuery()->where('ins.estado', 1);

        if (! empty($filtros['gestion'])) {
            $q->where('ins.gestion', $filtros['gestion']);
        }
        if (! empty($filtros['periodo'])) {
            $q->where('ins.periodo', $filtros['periodo']);
        }

        $wrapped = DB::table(DB::raw("({$q->toSql()}) as sub"))->mergeBindings($q);

        return $wrapped
            ->selectRaw("
                {$expresionAgrupacion} as {$aliasAgrupacion},
                COUNT(*) as inscritos,
                COALESCE(SUM(sub.total_pagado), 0) as total_cobrado,
                COALESCE(SUM(GREATEST(sub.total_a_pagar - sub.total_pagado, 0)), 0) as total_pendiente,
                COALESCE(SUM(sub.total_a_pagar), 0) as total_plan,
                SUM(CASE WHEN sub.total_a_pagar > 0 AND sub.total_pagado >= sub.total_a_pagar THEN 1 ELSE 0 END) as pagados,
                SUM(CASE WHEN sub.total_pagado > 0 AND NOT (sub.total_a_pagar > 0 AND sub.total_pagado >= sub.total_a_pagar) THEN 1 ELSE 0 END) as parciales,
                SUM(CASE WHEN sub.total_pagado <= 0 THEN 1 ELSE 0 END) as pendientes
            ")
            ->groupBy(DB::raw($expresionAgrupacion))
            ->orderByDesc('total_cobrado')
            ->get();
    }

    public function reportePorVendedor(array $filtros = []): array
    {

        $filas = $this->agregarPorColumna($filtros, 'sub.id_vendedor', 'id_vendedor');

        $nombres = DB::table('t_usuario as vend')
            ->whereIn('vend.id_us', $filas->pluck('id_vendedor')->filter()->all())
            ->whereRaw('vend.id_us_reg = (SELECT MIN(v2.id_us_reg) FROM t_usuario v2 WHERE v2.id_us = vend.id_us)')
            ->selectRaw("vend.id_us, TRIM(CONCAT(COALESCE(vend.nombre,''), ' ', COALESCE(vend.appaterno,''))) as nombre")
            ->pluck('nombre', 'id_us');

        $porVendedor = $filas->map(fn ($r) => [
            'id_vendedor'     => $r->id_vendedor,
            'vendedor_nombre' => $r->id_vendedor ? ($nombres->get($r->id_vendedor) ?: 'Sin vendedor') : 'Sin vendedor',
            'inscritos'       => (int) $r->inscritos,
            'total_cobrado'   => (float) $r->total_cobrado,
            'total_pendiente' => (float) $r->total_pendiente,
            'total_plan'      => (float) $r->total_plan,
            'pagados'         => (int) $r->pagados,
            'parciales'       => (int) $r->parciales,
            'pendientes'      => (int) $r->pendientes,
        ])->values()->all();

        return ['data' => $porVendedor];
    }

    public function reportePorCanal(array $filtros = []): array
    {

        $filas = $this->agregarPorColumna($filtros, "COALESCE(sub.canal_venta, 'admin')", 'canal_venta');

        $porCanal = $filas->map(fn ($r) => [
            'canal'           => $r->canal_venta ?? 'admin',
            'inscritos'       => (int) $r->inscritos,
            'total_cobrado'   => (float) $r->total_cobrado,
            'total_pendiente' => (float) $r->total_pendiente,
            'total_plan'      => (float) $r->total_plan,
            'pagados'         => (int) $r->pagados,
            'parciales'       => (int) $r->parciales,
            'pendientes'      => (int) $r->pendientes,
        ])->values()->all();

        return ['data' => $porCanal];
    }

    public function proyeccionCobros(int $meses = 6): array
    {
        $mesExpr     = SqlCompat::dateFormat('fp.fecha_fin', '%Y-%m');
        $gestionExpr = SqlCompat::dateFormat('fp.fecha_fin', '%Y');

        $cuotas = DB::table('t_fechapago as fp')
            ->leftJoin('t_inscripcion as ins', 'ins.id_plan', '=', 'fp.id_plan')
            ->leftJoin('t_pago as p', function ($j) {
                $j->on('p.id_fechapago', '=', 'fp.id_fechapago')
                  ->where('p.estado', 1);
            })
            ->leftJoin('t_programa as prog', function ($j) {
                $j->on('prog.id_imp', '=', 'ins.id_imp')
                  ->whereRaw('prog.id_us_reg = (SELECT MIN(p2.id_us_reg) FROM t_programa p2 WHERE p2.id_imp = prog.id_imp)');
            })
            ->where('fp.estado', 1)
            ->where('ins.estado', 1)
            ->whereNull('p.id_pago')
            ->whereNotNull('fp.fecha_fin')
            ->whereBetween('fp.fecha_fin', [
                now()->format('Y-m-d'),
                now()->addMonths($meses)->format('Y-m-d'),
            ])
            ->select([
                DB::raw("{$mesExpr} as mes_key"),
                DB::raw("{$gestionExpr} as gestion"),
                DB::raw('COUNT(*) as cuotas_pendientes'),
                DB::raw('SUM(CAST(fp.monto_a_pagar AS DECIMAL(12,2))) as monto_proyectado'),
            ])
            ->groupByRaw("{$mesExpr}, {$gestionExpr}")
            ->orderBy('mes_key')
            ->get();

        return ['data' => $cuotas->all(), 'meses' => $meses];
    }
}
