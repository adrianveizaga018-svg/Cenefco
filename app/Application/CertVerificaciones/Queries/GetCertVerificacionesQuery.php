<?php

namespace App\Application\CertVerificaciones\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetCertVerificacionesQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?int          $certificadoId    = null,
        public ?string       $codigoConsultado = null,
        public ?string       $resultado        = null,
    ) {}
}
