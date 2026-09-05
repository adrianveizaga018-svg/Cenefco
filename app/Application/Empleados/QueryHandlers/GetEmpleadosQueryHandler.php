<?php

namespace App\Application\Empleados\QueryHandlers;

use App\Application\Empleados\Queries\GetEmpleadosQuery;
use App\Domain\Empleados\Contracts\EmpleadoRepositoryInterface;

class GetEmpleadosQueryHandler
{
    public function __construct(private readonly EmpleadoRepositoryInterface $repository) {}

    public function handle(GetEmpleadosQuery $query): array
    {
        return $this->repository->paginate($query->pagination);
    }
}
