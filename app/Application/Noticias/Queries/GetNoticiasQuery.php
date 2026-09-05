<?php

namespace App\Application\Noticias\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetNoticiasQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public bool $soloActivos = false,
    ) {}
}
