<?php

namespace App\Application\PlanesAcademicos\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetPlanesAcademicosQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public bool $conInactivos = false,
        public ?int $idCatplan = null,
        public ?int $idMat = null,
        public bool $soloValidos = false, // filtra planes con costo=0 o sin cuotas
    ) {}
}
