<?php

namespace App\Application\Trivia\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetTriviaCategoriasQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public bool $soloActivos = false,
    ) {}
}
