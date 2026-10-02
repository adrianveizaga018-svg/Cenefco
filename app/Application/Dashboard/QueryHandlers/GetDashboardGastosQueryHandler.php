<?php

namespace App\Application\Dashboard\QueryHandlers;

use App\Application\Dashboard\Queries\GetDashboardGastosQuery;
use App\Application\Gastos\Queries\GetGastosPendientesDelMesQuery;
use App\Application\Gastos\Queries\GetResumenMesQuery;
use App\Application\Gastos\QueryHandlers\GetGastosPendientesDelMesQueryHandler;
use App\Application\Gastos\QueryHandlers\GetResumenMesQueryHandler;
use App\Application\Ingresos\Queries\GetResumenIngresosQuery;
use App\Application\Ingresos\QueryHandlers\GetResumenIngresosQueryHandler;

class GetDashboardGastosQueryHandler
{
    public function __construct(
        private readonly GetResumenIngresosQueryHandler $resumenIngresosHandler,
        private readonly GetResumenMesQueryHandler       $resumenGastosHandler,
        private readonly GetGastosPendientesDelMesQueryHandler $pendientesHandler,
    ) {}

    public function handle(GetDashboardGastosQuery $query): array
    {
        $resumenIngresos = $this->resumenIngresosHandler->handle(new GetResumenIngresosQuery());
        $resumenGastos   = $this->resumenGastosHandler->handle(new GetResumenMesQuery($query->anio, $query->mes));
        $pendientes      = $this->pendientesHandler->handle(new GetGastosPendientesDelMesQuery($query->anio, $query->mes));

        $totalIngresosMes = $resumenIngresos['total_mes'];
        $totalGastosMes   = $resumenGastos['resumen']['total_mes'];

        return [
            'anio' => $query->anio,
            'mes'  => $query->mes,
            'resumen_mes' => [
                'total_ingresos' => $totalIngresosMes,
                'total_gastos'   => $totalGastosMes,
                'balance'        => $totalIngresosMes - $totalGastosMes,
                'n_pagos_mes'    => $resumenIngresos['n_pagos_mes'],
            ],
            'gastos_por_categoria'     => $resumenGastos['resumen']['por_categoria'],
            'gastos_por_linea_negocio' => $resumenGastos['por_linea_negocio'],
            'gastos_pendientes'        => $pendientes,
        ];
    }
}
