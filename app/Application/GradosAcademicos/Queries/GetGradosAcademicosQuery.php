<?php

namespace App\Application\GradosAcademicos\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetGradosAcademicosQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?string       $query,
        public bool          $soloActivos = false,
    ) {}
}
