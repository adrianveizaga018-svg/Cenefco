<?php

namespace App\Application\Convenios\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetConveniosQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public bool $soloActivos = false,
    ) {}
}
