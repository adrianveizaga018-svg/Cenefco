<?php

namespace App\Application\Carreras\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetCarrerasQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public bool $conInactivos = false,
    ) {}
}
