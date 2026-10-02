<?php

namespace App\Application\MediosPago\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetMediosPagoQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?string       $query,
        public bool          $soloActivos = false,
    ) {}
}
