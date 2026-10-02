<?php

namespace App\Application\Areas\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetAreasQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public bool          $soloActivas = false,
    ) {}
}
