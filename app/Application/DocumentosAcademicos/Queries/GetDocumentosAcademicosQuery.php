<?php

namespace App\Application\DocumentosAcademicos\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetDocumentosAcademicosQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public bool $conInactivos = false,
        public ?int $idUs = null,
        public ?int $idFechapago = null,
        public ?int $idFechadoc = null,
    ) {}
}
