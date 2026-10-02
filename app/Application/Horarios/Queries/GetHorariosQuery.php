<?php

namespace App\Application\Horarios\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetHorariosQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public bool $conInactivos = false,
        public ?int $idImp = null,
    ) {}
}
