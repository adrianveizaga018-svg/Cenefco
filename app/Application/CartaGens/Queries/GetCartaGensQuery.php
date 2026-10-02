<?php

namespace App\Application\CartaGens\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetCartaGensQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?int          $idUs,
        public ?int          $idCartamod,
        public bool          $conInactivos,
    ) {}
}
