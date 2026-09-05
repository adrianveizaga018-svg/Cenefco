<?php

namespace App\Application\Notas\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetNotasQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public bool $conInactivos = false,
        public ?int $idUs = null,
        public ?int $idImp = null,
        public ?int $idMat = null,
        public ?string $periodo = null,
        public ?string $gestion = null,
    ) {}
}
