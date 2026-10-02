<?php

namespace App\Application\Empleados\QueryHandlers;

use App\Application\Empleados\Queries\GetEmpleadosActivosQuery;
use App\Domain\Empleados\Contracts\EmpleadoRepositoryInterface;

class GetEmpleadosActivosQueryHandler
{
    public function __construct(private readonly EmpleadoRepositoryInterface $repository) {}

    public function handle(GetEmpleadosActivosQuery $query): array
    {
        return $this->repository->findAllActivos();
    }
}
