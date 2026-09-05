<?php

namespace App\Application\UsuariosPlanDoc\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetUsuariosPlanDocQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?int $idUs,
        public ?int $idPlanDoc,
        public bool $conInactivos,
    ) {}
}
