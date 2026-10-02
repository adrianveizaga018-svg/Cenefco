<?php

namespace App\Application\Empleados\Handlers;

use App\Application\Empleados\Commands\DeleteEmpleadoCommand;
use App\Domain\Empleados\Contracts\EmpleadoRepositoryInterface;

class DeleteEmpleadoHandler
{
    public function __construct(private readonly EmpleadoRepositoryInterface $repository) {}

    public function handle(DeleteEmpleadoCommand $c): bool
    {
        return $this->repository->delete($c->id);
    }
}
