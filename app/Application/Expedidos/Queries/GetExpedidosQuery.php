<?php

namespace App\Application\Expedidos\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetExpedidosQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?string       $query,
    ) {}
}
