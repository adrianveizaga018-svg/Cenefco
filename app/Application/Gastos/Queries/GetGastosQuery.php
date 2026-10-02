<?php

namespace App\Application\Gastos\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetGastosQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public array         $filtros = [],
    ) {}
}
