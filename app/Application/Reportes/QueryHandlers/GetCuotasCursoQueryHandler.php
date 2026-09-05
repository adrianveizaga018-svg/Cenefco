<?php

namespace App\Application\Reportes\QueryHandlers;

use App\Application\Reportes\Queries\GetCuotasCursoQuery;
use App\Domain\Reportes\Contracts\ReporteRepositoryInterface;

class GetCuotasCursoQueryHandler
{
    public function __construct(
        private readonly ReporteRepositoryInterface $reporteRepository,
    ) {}

    public function handle(GetCuotasCursoQuery $query): array
    {
        return $this->reporteRepository->cuotasCurso(
            idImp:           $query->idImp,
            fechaInicio:     $query->fechaInicio,
            fechaFin:        $query->fechaFin,
            conInactivos:    $query->conInactivos,
            idImpPermitidos: $query->idImpPermitidos,
        );
    }
}
