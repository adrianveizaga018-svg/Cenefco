<?php

namespace App\Application\FechasDoc\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetFechasDocQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public bool $conInactivos = false,
        public ?int $idPlandoc = null,
    ) {}
}
