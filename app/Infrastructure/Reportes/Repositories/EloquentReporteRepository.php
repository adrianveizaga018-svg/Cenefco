<?php

namespace App\Infrastructure\Reportes\Repositories;

use App\Application\Pagos\Services\PagoCalculadorService;
use App\Application\Reportes\DTOs\ReporteCuotaDTO;
use App\Application\Reportes\DTOs\ReportePagoDTO;
use App\Application\Reportes\DTOs\ReporteParticipanteDTO;
use App\Application\Reportes\DTOs\ReporteVentasPorPeriodoDTO;
use App\Domain\Reportes\Contracts\ReporteRepositoryInterface;
use Illuminate\Support\Facades\DB;

class EloquentReporteRepository implements ReporteRepositoryInterface
{
    public function __construct(
        private readonly PagoCalculadorService $calculador,
    ) {}

    public function ventasPorPeriodo(string $fechaInicio, string $fechaFin, ?int $usuarioId): array
    {
        $planSub = DB::table('t_fechapago')
            ->select('id_plan', DB::raw('SUM(CAST(monto_a_pagar AS DECIMAL(12,2))) as total_a_pagar'))
            ->where('estado', 1)
            ->groupBy('id_plan');

        $pagoSub = DB::table('t_pago as p')
            ->join('t_inscripcion as ins2', function ($j) {
                $j->on('ins2.id_us', '=', 'p.id_us')
                  ->where(function ($jj) {
                      $jj->whereColumn('p.id_ins', 'ins2.id_ins')
                         ->orWhereNull('p.id_ins');
                  });
            })
            ->join('t_fechapago as fp2', function ($j) {
                $j->on('fp2.id_plan', '=', 'ins2.id_plan')
                  ->whereColumn('fp2.id_fechapago', 'p.id_fechapago');
            }, null, null, 'left')
            ->where('p.estado', 1)
            ->whereBetween('p.fecha_deposito', [$fechaInicio, $fechaFin])
            ->where(function ($q) {
                $q->whereNotNull('p.id_ins')
                  ->orWhere(function ($qq) {
                      $qq->whereNull('p.id_ins')
                         ->where(function ($qqq) {
                             $qqq->whereNotNull('fp2.id_fechapago')
                                 ->orWhere('p.pago_extra', 1);
                         });
                  });
            })
            ->selectRaw('ins2.id_ins,
                SUM(CAST(p.monto_pagado AS DECIMAL(12,2))) as total_pagado,
                COUNT(p.id_pago) as nro_pagos')
            ->groupBy('ins2.id_ins');

        $rows = DB::table('t_inscripcion as ins')
            ->leftJoinSub($planSub, 'plan_agg', 'plan_agg.id_plan', '=', 'ins.id_plan')
            ->leftJoinSub($pagoSub, 'pago_agg', 'pago_agg.id_ins', '=', 'ins.id_ins')
            ->where('ins.estado', 1)
            ->when($usuarioId, fn ($q) => $q->where('ins.id_us', $usuarioId))
            ->selectRaw("
                COALESCE(ins.gestion, 'Sin gestión') as gestion,
                COALESCE(ins.periodo, 'Sin periodo') as periodo,
                COUNT(DISTINCT ins.id_ins)                                     AS total_inscripciones,
                COALESCE(SUM(pago_agg.nro_pagos), 0)                          AS total_pagos,
                COALESCE(SUM(plan_agg.total_a_pagar), 0)                       AS total_a_pagar,
                COALESCE(SUM(pago_agg.total_pagado), 0)                        AS total_pagado,
                SUM(CASE
                    WHEN COALESCE(plan_agg.total_a_pagar, 0) > 0
                     AND COALESCE(pago_agg.total_pagado, 0) >= plan_agg.total_a_pagar THEN 1
                    ELSE 0
                END)                                                            AS pagados,
                SUM(CASE
                    WHEN COALESCE(pago_agg.total_pagado, 0) > 0
                     AND COALESCE(pago_agg.total_pagado, 0) < COALESCE(plan_agg.total_a_pagar, 0) THEN 1
                    ELSE 0
                END)                                                            AS parciales,
                SUM(CASE
                    WHEN COALESCE(pago_agg.total_pagado, 0) = 0 THEN 1
                    ELSE 0
                END)                                                            AS pendientes
            ")
            ->groupBy('ins.gestion', 'ins.periodo')
            ->orderBy('ins.gestion', 'desc')
            ->orderBy('ins.periodo', 'desc')
            ->get();

        return $rows->map(fn ($row) => ReporteVentasPorPeriodoDTO::fromRow($row))->all();
    }

    public function cuotasCurso(?int $idImp, ?string $fechaInicio, ?string $fechaFin, bool $conInactivos, ?array $idImpPermitidos = null): array
    {
        $participantesQuery = DB::table('t_inscripcion as ins')
            ->leftJoin('t_usuario as u', function ($j) {
                $j->on('ins.id_us', '=', 'u.id_us')
                  ->whereRaw('u.id_us_reg = (SELECT MIN(u2.id_us_reg) FROM t_usuario u2 WHERE u2.id_us = u.id_us)');
            })
            ->leftJoin('t_imparte as imp', function ($j) {
                $j->on('ins.id_imp', '=', 'imp.id_imp')
                  ->whereRaw('imp.id_us_reg = (SELECT MIN(i2.id_us_reg) FROM t_imparte i2 WHERE i2.id_imp = imp.id_imp)');
            })
            ->leftJoin('t_materia as m', function ($j) {
                $j->on('imp.id_mat', '=', 'm.id_mat')
                  ->whereRaw('m.id_us_reg = (SELECT MIN(m2.id_us_reg) FROM t_materia m2 WHERE m2.id_mat = m.id_mat)');
            })
            ->select([
                'ins.id_ins', 'ins.id_us', 'ins.id_imp', 'ins.id_plan', 'ins.periodo', 'ins.gestion',
                DB::raw("TRIM(CONCAT(COALESCE(u.nombre,''), ' ', COALESCE(u.appaterno,''))) as estudiante_nombre"),
                'u.ci as estudiante_ci', 'u.email as estudiante_email', 'u.celular as estudiante_celular',
                DB::raw("COALESCE((SELECT p2.nombre_programa FROM t_programa p2 WHERE p2.id_imp = imp.id_imp ORDER BY p2.id_us_reg LIMIT 1), m.nombremat) as curso_nombre"),

                DB::raw("(SELECT p3.costo_monto FROM t_programa p3 WHERE p3.id_imp = imp.id_imp ORDER BY p3.id_us_reg LIMIT 1) as costo_monto"),
            ]);

        if (! $conInactivos) {
            $participantesQuery->where('ins.estado', 1);
        }
        if ($idImp !== null) {
            $participantesQuery->where('ins.id_imp', $idImp);
        }
        if ($idImpPermitidos !== null) {
            $participantesQuery->whereIn('ins.id_imp', $idImpPermitidos);
        }

        $participantes = $participantesQuery->orderBy('estudiante_nombre')->get();

        $totalesVacios = [
            'total_plan' => 0.0, 'total_pagado' => 0.0, 'pendiente' => 0.0,
            'al_dia' => 0, 'en_mora' => 0, 'completo' => 0, 'sin_pagos' => 0, 'sin_plan' => 0,
            'total_participantes' => 0,
        ];

        if ($participantes->isEmpty()) {
            return ['participantes' => [], 'totales' => $totalesVacios];
        }

        $idsUs   = $participantes->pluck('id_us')->unique()->all();
        $idsPlan = $participantes->pluck('id_plan')->filter()->unique()->all();

        $cuotasQuery = DB::table('t_fechapago')
            ->whereIn('id_plan', $idsPlan)
            ->where('estado', 1);
        if ($fechaInicio !== null) {
            $cuotasQuery->where('fecha_fin', '>=', $fechaInicio);
        }
        if ($fechaFin !== null) {
            $cuotasQuery->where('fecha_fin', '<=', $fechaFin);
        }
        $cuotasPorPlan = $cuotasQuery->orderBy('id_fechapago')->get()->groupBy('id_plan');

        $pagosPorUs = DB::table('t_pago')
            ->whereIn('id_us', $idsUs)
            ->where('estado', 1)
            ->get()
            ->groupBy('id_us');

        $anticipoHuerfanoAsignadoA = [];
        foreach ($participantes as $part) {
            foreach ($pagosPorUs->get($part->id_us) ?? collect() as $p) {
                if ($p->id_ins === null && (int) $p->pago_extra === 1 && $p->id_fechapago === null) {
                    $anticipoHuerfanoAsignadoA[$p->id_pago] ??= $part->id_ins;
                }
            }
        }

        $resultado = [];
        $totales   = $totalesVacios;
        unset($totales['total_participantes']);

        foreach ($participantes as $part) {
            $cuotasDelPlan = $part->id_plan ? ($cuotasPorPlan->get($part->id_plan) ?? collect()) : collect();
            $idsFechapagoDelPlan = $cuotasDelPlan->pluck('id_fechapago')->all();

            $pagosDelUs = $pagosPorUs->get($part->id_us) ?? collect();
            $pagosDeEstaInscripcion = $pagosDelUs->filter(function ($p) use ($part, $idsFechapagoDelPlan, $anticipoHuerfanoAsignadoA) {
                if ($p->id_ins !== null) {
                    return (int) $p->id_ins === (int) $part->id_ins;
                }
                if (in_array($p->id_fechapago, $idsFechapagoDelPlan)) {
                    return true;
                }
                if ((int) $p->pago_extra === 1 && $p->id_fechapago === null) {
                    return ($anticipoHuerfanoAsignadoA[$p->id_pago] ?? null) === $part->id_ins;
                }

                return false;
            });

            $costoMonto = $part->id_plan ? 0.0 : (float) ($part->costo_monto ?? 0);

            $resumen = $part->id_plan
                ? $this->calculador->calcular($part->id_plan, $pagosDeEstaInscripcion)
                : $this->calculador->calcularConCostoFijo($costoMonto, $pagosDeEstaInscripcion);

            $totalPlan     = $resumen->total_plan;
            $pendiente     = $resumen->pendiente;
            $cuotasTotales = $resumen->cuotas_totales;
            $cuotasPagadas = $resumen->cuotas_pagadas;

            $cuotasDto = [];
            foreach ($cuotasDelPlan as $cuota) {
                $pagosDeEstaCuota = $pagosDeEstaInscripcion
                    ->where('id_fechapago', $cuota->id_fechapago)
                    ->where('pago_extra', '!=', 1);

                $estadosVerif = $pagosDeEstaCuota->pluck('estado_verificacion')->filter()->unique();
                $estadoVerif  = $estadosVerif->isEmpty()
                    ? null
                    : ($estadosVerif->every(fn ($e) => $e === 'verificado')
                        ? 'verificado'
                        : ($estadosVerif->contains('observado') ? 'observado' : 'pendiente'));

                $cuotasDto[] = ReporteCuotaDTO::fromRow((object) [
                    'id_fechapago'              => $cuota->id_fechapago,
                    'cuota_nro'                 => $cuota->nro_pago,
                    'cuota_monto'               => $cuota->monto_a_pagar,
                    'cuota_fecha_inicio'        => $cuota->fecha_inicio,
                    'cuota_fecha_fin'           => $cuota->fecha_fin,
                    'tipo_tramite'              => $cuota->tipo_tramite,
                    'cuota_monto_pagado'        => $pagosDeEstaCuota->sum(fn ($p) => (float) $p->monto_pagado),
                    'cuota_nro_pagos'           => $pagosDeEstaCuota->count(),
                    'cuota_ultima_fecha_pago'   => $pagosDeEstaCuota->max('fecha_deposito'),
                    'cuota_estado_verificacion' => $estadoVerif,
                ]);
            }

            $tieneFiltroFecha = $fechaInicio !== null || $fechaFin !== null;
            if ($tieneFiltroFecha) {
                $tienePagoEnRango = $pagosDeEstaInscripcion->contains(function ($p) use ($fechaInicio, $fechaFin) {
                    if (! $p->fecha_deposito) return false;
                    if ($fechaInicio !== null && $p->fecha_deposito < $fechaInicio) return false;
                    if ($fechaFin !== null && $p->fecha_deposito > $fechaFin) return false;
                    return true;
                });

                if (empty($cuotasDto) && ! $tienePagoEnRango) {
                    continue;
                }
            }

            $cuotaNroPorFechapago = $cuotasDelPlan->pluck('nro_pago', 'id_fechapago');
            $pagosDto = $pagosDeEstaInscripcion
                ->sortBy('fecha_deposito')
                ->map(fn ($p) => ReportePagoDTO::fromRow($p, $cuotaNroPorFechapago->get($p->id_fechapago)))
                ->values()
                ->all();

            $cuotasVencidas = collect($cuotasDto)->where('estado_cuota', 'vencida')->count();

            $estadoGeneral = match (true) {
                $resumen->plan_no_asignado && $costoMonto <= 0 => 'sin_plan',
                $cuotasTotales > 0 && $cuotasPagadas >= $cuotasTotales => 'completo',
                $cuotasVencidas > 0 => 'en_mora',
                $resumen->total_pagado <= 0 => 'sin_pagos',
                default => 'al_dia',
            };

            $resultado[] = new ReporteParticipanteDTO(
                id_ins:              (int) $part->id_ins,
                id_us:                (int) $part->id_us,
                id_imp:               (int) $part->id_imp,
                estudiante_nombre:    trim($part->estudiante_nombre) ?: '(sin nombre)',
                estudiante_ci:        $part->estudiante_ci,
                estudiante_email:     $part->estudiante_email,
                estudiante_celular:   $part->estudiante_celular,
                curso_nombre:         $part->curso_nombre,
                periodo:              $part->periodo,
                gestion:              $part->gestion,
                total_cuotas:         $cuotasTotales,
                cuotas_pagadas:       $cuotasPagadas,
                cuotas_vencidas:      $cuotasVencidas,
                total_plan:           $totalPlan,
                total_pagado:         $resumen->total_pagado,
                total_anticipos:      $resumen->total_anticipos,
                pendiente:            $pendiente,
                plan_no_asignado:     $resumen->plan_no_asignado && $costoMonto <= 0,
                estado_general:       $estadoGeneral,
                cuotas:               $cuotasDto,
                pagos:                $pagosDto,
            );

            $totales['total_plan']   += $totalPlan;
            $totales['total_pagado'] += $resumen->total_pagado;
            $totales['pendiente']    += ($pendiente ?? 0.0);
            $totales[$estadoGeneral] += 1;
        }

        $totales['total_participantes'] = count($resultado);

        return ['participantes' => $resultado, 'totales' => $totales];
    }
}
