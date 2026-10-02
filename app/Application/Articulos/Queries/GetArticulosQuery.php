<?php

namespace App\Application\Articulos\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetArticulosQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?string       $query,
        public ?string       $estadoWeb,
        public bool          $conInactivos,
    ) {}
}
