<?php

namespace App\Application\Comisiones\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetComisionesQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?int    $vendedorId = null,
        public ?string $estado     = null,
    ) {}
}
