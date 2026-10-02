<?php

namespace App\Application\SugerenciasReclamos\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetSugerenciasReclamosQuery
{
    public function __construct(public PaginationDTO $pagination) {}
}
