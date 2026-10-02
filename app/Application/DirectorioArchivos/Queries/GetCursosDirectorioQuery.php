<?php

namespace App\Application\DirectorioArchivos\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetCursosDirectorioQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?array $idImpPermitidos,
    ) {}
}
