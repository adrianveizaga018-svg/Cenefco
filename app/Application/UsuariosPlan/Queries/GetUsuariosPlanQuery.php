<?php

namespace App\Application\UsuariosPlan\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetUsuariosPlanQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?int $idUs,
        public ?int $idPlan,
        public bool $conInactivos,
    ) {}
}
