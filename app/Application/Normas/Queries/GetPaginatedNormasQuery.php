<?php

namespace App\Application\Normas\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetPaginatedNormasQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public array $filters = [],
    ) {}
}
