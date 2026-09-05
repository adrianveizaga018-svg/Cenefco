<?php

namespace App\Application\Ingresos\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetIngresosQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?string       $desde = null,
        public ?string       $hasta = null,
    ) {}
}
