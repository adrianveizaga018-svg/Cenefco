<?php

namespace App\Application\Autoridades\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetAutoridadesQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public bool $soloActivos = false,
        public bool $soloPublicadas = false,
    ) {}
}
