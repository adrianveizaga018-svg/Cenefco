<?php

namespace App\Application\CompromisosCobro\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetCompromisosCobroQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?string       $estado = null,
        public ?array        $idImpPermitidos = null,
    ) {}
}
