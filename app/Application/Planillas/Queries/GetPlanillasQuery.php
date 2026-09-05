<?php

namespace App\Application\Planillas\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetPlanillasQuery
{
    public function __construct(public PaginationDTO $pagination) {}
}
