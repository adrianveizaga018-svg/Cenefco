<?php

namespace App\Application\Certificados\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetCertificadosQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?int          $usuarioId  = null,
        public ?int          $imparteId  = null,
        public ?string       $estado     = null,
    ) {}
}
