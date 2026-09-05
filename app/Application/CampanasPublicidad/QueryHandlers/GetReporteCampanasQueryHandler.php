<?php

namespace App\Application\CampanasPublicidad\QueryHandlers;

use App\Application\CampanasPublicidad\Queries\GetReporteCampanasQuery;
use App\Domain\CampanasPublicidad\Contracts\CampanaPublicidadRepositoryInterface;

class GetReporteCampanasQueryHandler
{
    public function __construct(private readonly CampanaPublicidadRepositoryInterface $repository) {}

    public function handle(GetReporteCampanasQuery $query): array
    {
        return $this->repository->reportePorCurso($query->fechaInicio, $query->fechaFin);
    }
}
