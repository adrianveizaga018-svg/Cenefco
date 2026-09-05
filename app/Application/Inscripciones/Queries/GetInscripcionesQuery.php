<?php

namespace App\Application\Inscripciones\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetInscripcionesQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public bool $conInactivos = false,
        public ?int $idUs = null,
        public ?int $idImp = null,
        public ?int $programaId = null,
        public ?string $periodo = null,
        public ?string $gestion = null,
        public ?array $idImpPermitidos = null,
    ) {}
}
