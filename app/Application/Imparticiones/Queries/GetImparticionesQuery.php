<?php

namespace App\Application\Imparticiones\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetImparticionesQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public bool $conInactivos = false,
        public ?string $periodo = null,
        public ?string $gestion = null,
        public ?int $idMat = null,
        public ?int $idUs = null,
    ) {}
}
