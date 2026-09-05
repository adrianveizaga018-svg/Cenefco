<?php

namespace App\Application\PlanesAcademicos\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetPlanesAcademicosQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public bool $conInactivos = false,
        public ?int $idCatplan = null,
        public ?int $idMat = null,
    ) {}
}
