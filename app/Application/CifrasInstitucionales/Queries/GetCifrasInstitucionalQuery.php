<?php

namespace App\Application\CifrasInstitucionales\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetCifrasInstitucionalQuery
{
    public function __construct(
        public ?PaginationDTO $pagination = null,
        public bool $publicoSoloActivas = false,
    ) {}
}
