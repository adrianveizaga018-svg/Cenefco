<?php

namespace App\Application\BannersPortal\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetBannersPortalQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public bool          $soloActivos = false,
    ) {}
}
