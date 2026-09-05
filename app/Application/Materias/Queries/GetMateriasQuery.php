<?php

namespace App\Application\Materias\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetMateriasQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public bool $conInactivos = false,
    ) {}
}
