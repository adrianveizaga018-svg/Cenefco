<?php

namespace App\Application\Boletines\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetBoletinesQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?string       $query,
        public bool          $conInactivos,
    ) {}
}
