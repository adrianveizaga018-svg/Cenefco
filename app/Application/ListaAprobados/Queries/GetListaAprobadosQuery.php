<?php

namespace App\Application\ListaAprobados\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetListaAprobadosQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?int          $imparteId          = null,
        public ?int          $usuarioId          = null,
        public ?string       $condicion          = null,
        public ?string       $estadoCertificado  = null,
    ) {}
}
