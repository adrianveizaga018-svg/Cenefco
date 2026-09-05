<?php

namespace App\Application\Resenas\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetResenasQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?string       $estado     = null,
        public ?int          $programaId = null,
    ) {}
}
