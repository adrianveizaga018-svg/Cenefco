<?php

namespace App\Application\Empleados\QueryHandlers;

use App\Application\Empleados\Queries\GetEmpleadoByIdQuery;
use App\Domain\Empleados\Contracts\EmpleadoRepositoryInterface;

class GetEmpleadoByIdQueryHandler
{
    public function __construct(private readonly EmpleadoRepositoryInterface $repository) {}

    public function handle(GetEmpleadoByIdQuery $query): mixed
    {
        return $this->repository->findById($query->id);
    }
}
