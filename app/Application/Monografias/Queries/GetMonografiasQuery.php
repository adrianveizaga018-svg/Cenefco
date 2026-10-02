<?php

namespace App\Application\Monografias\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetMonografiasQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?string       $query,
        public bool          $conInactivos,
    ) {}
}
