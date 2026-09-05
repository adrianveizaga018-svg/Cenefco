<?php

namespace App\Application\Reportes\QueryHandlers;

use App\Application\Reportes\Queries\GetVentasPorPeriodoQuery;
use App\Domain\Reportes\Contracts\ReporteRepositoryInterface;

class GetVentasPorPeriodoQueryHandler
{
    public function __construct(
        private readonly ReporteRepositoryInterface $reporteRepository,
    ) {}

    public function handle(GetVentasPorPeriodoQuery $query): array
    {
        return $this->reporteRepository->ventasPorPeriodo(
            fechaInicio: $query->fechaInicio,
            fechaFin:    $query->fechaFin,
            usuarioId:   $query->usuarioId,
        );
    }
}
