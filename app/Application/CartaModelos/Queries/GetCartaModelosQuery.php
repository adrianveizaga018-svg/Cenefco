<?php

namespace App\Application\CartaModelos\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetCartaModelosQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?string       $query,
        public bool          $conInactivos,
    ) {}
}
