<?php

namespace App\Application\WebMenus\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetWebMenusQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?string $query,
    ) {}
}
