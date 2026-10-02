<?php

namespace App\Application\Cursos\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetCursosQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public bool $soloPublicados = false,
        public ?array $idsPermitidos = null,
    ) {}
}
