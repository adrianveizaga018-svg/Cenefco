<?php

namespace App\Application\BloquesPlantilla\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetBloquesPlantillaQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public bool $conInactivos,
    ) {}
}
