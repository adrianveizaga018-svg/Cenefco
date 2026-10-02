<?php

namespace App\Application\Aliados\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetAliadosQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public bool          $soloActivos = false,
    ) {}
}
