<?php

namespace App\Application\Profesiones\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetProfesionesQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?string       $query,
        public bool          $soloActivos = false,
    ) {}
}
