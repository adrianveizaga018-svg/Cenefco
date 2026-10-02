<?php

namespace App\Application\CampanasLeads\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetCampanasLeadQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?string       $estado = null,
    ) {}
}
