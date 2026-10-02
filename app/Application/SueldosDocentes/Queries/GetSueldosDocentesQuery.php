<?php

namespace App\Application\SueldosDocentes\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetSueldosDocentesQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?string       $periodo    = null,
        public ?string       $gestion    = null,
        public ?string       $estadoPago = null,
    ) {}
}
