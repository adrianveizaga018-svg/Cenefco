<?php

namespace App\Application\Ingresos\QueryHandlers;

use App\Application\Ingresos\Queries\GetIngresosQuery;
use App\Domain\Ingresos\Contracts\IngresoRepositoryInterface;

class GetIngresosQueryHandler
{
    public function __construct(
        private readonly IngresoRepositoryInterface $repository,
    ) {}

    public function handle(GetIngresosQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->desde, $query->hasta);
    }
}
