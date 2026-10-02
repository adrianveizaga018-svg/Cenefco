<?php

namespace App\Application\InscripcionesDiplomado\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetInscripcionesDiplomadoQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?string       $estado     = null,
        public ?int          $programaId = null,
    ) {}
}
