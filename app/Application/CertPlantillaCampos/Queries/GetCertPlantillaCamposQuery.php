<?php

namespace App\Application\CertPlantillaCampos\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetCertPlantillaCamposQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?int          $plantillaId = null,
        public bool          $soloActivos = false,
    ) {}
}
