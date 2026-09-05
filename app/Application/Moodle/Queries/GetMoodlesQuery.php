<?php

namespace App\Application\Moodle\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetMoodlesQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?string $query,
        public bool $conInactivos,
    ) {}
}
