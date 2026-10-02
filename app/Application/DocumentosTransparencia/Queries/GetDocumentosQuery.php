<?php

namespace App\Application\DocumentosTransparencia\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetDocumentosQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public bool $soloActivos = false,
    ) {}
}
