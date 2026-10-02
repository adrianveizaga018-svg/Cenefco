<?php

namespace App\Application\Pagos\Services;

use App\Application\Pagos\DTOs\ResumenPagoDTO;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PagoCalculadorService
{
    
    public function calcular(?int $planId, Collection $pagosActivos): ResumenPagoDTO
    {
        if (! $planId) {
            return ResumenPagoDTO::fromArray([
                'plan_no_asignado' => true,
                'total_pagado'     => $pagosActivos->sum(fn($p) => (float) $p->monto_pagado),
                'total_anticipos'  => 0.0,
                'cuotas_pagadas'   => 0,
                'cuotas_totales'   => 0,
                'total_plan'       => 0.0,
                'pendiente'        => null,
            ]);
        }

        $todasLasCuotas = DB::table('t_fechapago')
            ->where('id_plan', $planId)
            ->where('estado', 1)
            ->orderBy('id_fechapago')
            ->get();

        $cuotasCubiertasIds = $pagosActivos
            ->whereNotNull('id_fechapago')
            ->where('pago_extra', '!=', 1)
            ->pluck('id_fechapago')
            ->unique()
            ->values()
            ->toArray();

        $totalPagado    = (float) $pagosActivos->sum(fn($p) => (float) $p->monto_pagado);
        $totalAnticipos = (float) $pagosActivos->where('pago_extra', 1)->sum(fn($p) => (float) $p->monto_pagado);
        $cuotasTotales  = $todasLasCuotas->count();
        $totalPlan      = (float) $todasLasCuotas->sum(fn($c) => (float) $c->monto_a_pagar);

        $montoPendienteCuotas = (float) $todasLasCuotas
            ->filter(fn($c) => ! in_array($c->id_fechapago, $cuotasCubiertasIds))
            ->sum(fn($c) => (float) $c->monto_a_pagar);

        $pendiente = $cuotasTotales > 0
            ? max(0.0, $montoPendienteCuotas - $totalAnticipos)
            : max(0.0, $totalPlan - $totalPagado);

        return ResumenPagoDTO::fromArray([
            'plan_no_asignado' => false,
            'total_pagado'     => $totalPagado,
            'total_anticipos'  => $totalAnticipos,
            'cuotas_pagadas'   => count($cuotasCubiertasIds),
            'cuotas_totales'   => $cuotasTotales,
            'total_plan'       => $totalPlan,
            'pendiente'        => $pendiente,
        ]);
    }

    public function calcularConCostoFijo(float $costoMonto, Collection $pagosActivos): ResumenPagoDTO
    {
        $totalPagado    = (float) $pagosActivos->sum(fn($p) => (float) $p->monto_pagado);
        $totalAnticipos = (float) $pagosActivos->where('pago_extra', 1)->sum(fn($p) => (float) $p->monto_pagado);
        $completo       = $costoMonto > 0 && $totalPagado >= $costoMonto;

        return ResumenPagoDTO::fromArray([
            'plan_no_asignado' => true,
            'total_pagado'     => $totalPagado,
            'total_anticipos'  => $totalAnticipos,
            'cuotas_pagadas'   => $completo ? 1 : 0,
            'cuotas_totales'   => $costoMonto > 0 ? 1 : 0,
            'total_plan'       => $costoMonto,
            'pendiente'        => $costoMonto > 0 ? max(0.0, $costoMonto - $totalPagado) : null,
        ]);
    }
}
