<?php

namespace App\Application\CategoriasPrograma\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetCategoriasProgramaQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public bool $soloActivos = false,
    ) {}
}
