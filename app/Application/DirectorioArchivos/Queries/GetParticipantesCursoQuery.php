<?php

namespace App\Application\DirectorioArchivos\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetParticipantesCursoQuery
{
    public function __construct(
        public int $idImp,
        public PaginationDTO $pagination,
    ) {}
}
