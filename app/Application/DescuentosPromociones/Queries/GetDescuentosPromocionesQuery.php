<?php

namespace App\Application\DescuentosPromociones\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetDescuentosPromocionesQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public bool          $soloActivos = false,
    ) {}
}
