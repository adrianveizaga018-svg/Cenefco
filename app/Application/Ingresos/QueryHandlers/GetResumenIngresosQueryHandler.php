<?php

namespace App\Application\Ingresos\QueryHandlers;

use App\Application\Ingresos\Queries\GetResumenIngresosQuery;
use App\Domain\Ingresos\Contracts\IngresoRepositoryInterface;

class GetResumenIngresosQueryHandler
{
    public function __construct(
        private readonly IngresoRepositoryInterface $repository,
    ) {}

    public function handle(GetResumenIngresosQuery $query): array
    {
        return $this->repository->resumen();
    }
}
