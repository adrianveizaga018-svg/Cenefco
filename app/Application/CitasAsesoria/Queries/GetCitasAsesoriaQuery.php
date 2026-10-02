<?php

namespace App\Application\CitasAsesoria\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetCitasAsesoriaQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?string $query,
        public ?string $estado,
    ) {}
}
