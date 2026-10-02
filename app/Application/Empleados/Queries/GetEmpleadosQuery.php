<?php

namespace App\Application\Empleados\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetEmpleadosQuery
{
    public function __construct(public PaginationDTO $pagination) {}
}
