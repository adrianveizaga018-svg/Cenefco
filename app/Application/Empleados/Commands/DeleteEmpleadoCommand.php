<?php

namespace App\Application\Empleados\Commands;

final readonly class DeleteEmpleadoCommand
{
    public function __construct(public int $id) {}
}
