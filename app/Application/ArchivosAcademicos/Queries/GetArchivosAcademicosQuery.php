<?php

namespace App\Application\ArchivosAcademicos\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetArchivosAcademicosQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public bool $conInactivos,
    ) {}
}
