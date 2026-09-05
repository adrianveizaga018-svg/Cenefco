<?php

namespace App\Application\Trivia\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetTriviaPreguntasQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?int $categoriaId = null,
        public ?int $nivelId = null,
    ) {}
}
