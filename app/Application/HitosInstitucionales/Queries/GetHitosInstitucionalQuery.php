<?php

namespace App\Application\HitosInstitucionales\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetHitosInstitucionalQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public bool $soloActivos = false,
    ) {}
}
