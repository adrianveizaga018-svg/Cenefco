<?php

namespace App\Application\RevistasCientificas\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetRevistasCientificasQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?string       $query,
        public bool          $conInactivos,
    ) {}
}
