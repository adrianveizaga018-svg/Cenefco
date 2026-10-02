<?php

namespace App\Application\Etiquetas\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetEtiquetasQuery
{
    public function __construct(public PaginationDTO $pagination) {}
}
