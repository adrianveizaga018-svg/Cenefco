<?php

namespace App\Application\ProgramasAcademicos\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetProgramasAcademicosQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public bool $conInactivos = false,
        public ?int $idTipoprograma = null,
    ) {}
}
