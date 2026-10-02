<?php

namespace App\Application\Cartas\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetCartasQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?int          $idUs,
        public ?int          $idPlan,
        public bool          $conInactivos,
    ) {}
}
