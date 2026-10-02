<?php

namespace App\Domain\PlanDoc\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface PlanDocRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?string $query, bool $conInactivos): array;
}
