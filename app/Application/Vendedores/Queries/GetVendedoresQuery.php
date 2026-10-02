<?php

namespace App\Application\Vendedores\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetVendedoresQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?bool $activo = null,
    ) {}
}
