<?php

namespace App\Application\CertPlantillas\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetCertPlantillasQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?string       $tipo        = null,
        public bool          $soloActivos = false,
    ) {}
}
