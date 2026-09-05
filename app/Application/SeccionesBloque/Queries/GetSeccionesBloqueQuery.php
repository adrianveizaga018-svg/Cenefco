<?php

namespace App\Application\SeccionesBloque\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetSeccionesBloqueQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?int $idBloqueajustable,
        public bool $conInactivos,
    ) {}
}
