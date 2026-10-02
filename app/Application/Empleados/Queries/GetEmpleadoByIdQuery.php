<?php

namespace App\Application\Empleados\Queries;

final readonly class GetEmpleadoByIdQuery
{
    public function __construct(public int $id) {}
}
