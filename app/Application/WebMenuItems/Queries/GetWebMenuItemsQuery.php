<?php

namespace App\Application\WebMenuItems\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetWebMenuItemsQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public int $menuId,
    ) {}
}
