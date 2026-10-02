<?php

namespace App\Application\CampanasPublicidad\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetCampanasPublicidadQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public array $filtros = [],
    ) {}
}
