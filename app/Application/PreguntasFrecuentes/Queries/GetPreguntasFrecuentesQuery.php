<?php

namespace App\Application\PreguntasFrecuentes\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetPreguntasFrecuentesQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public bool $soloActivos = false,
    ) {}
}
