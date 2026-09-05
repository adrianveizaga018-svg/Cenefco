<?php

namespace App\Application\CampanasLeads\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetLeadsQuery
{
    public function __construct(
        public int           $campanaLeadId,
        public PaginationDTO $pagination,
    ) {}
}
