<?php

namespace App\Application\Pagos\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetPagosQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public array         $filters = [],
    ) {}
}
