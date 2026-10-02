<?php

namespace App\Application\Revistas\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetRevistasQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?string       $query,
        public bool          $conInactivos,
    ) {}
}
