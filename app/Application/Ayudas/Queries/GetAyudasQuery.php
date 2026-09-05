<?php

namespace App\Application\Ayudas\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetAyudasQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?int          $idUs,
        public ?string       $gestion,
        public bool          $conInactivos,
    ) {}
}
