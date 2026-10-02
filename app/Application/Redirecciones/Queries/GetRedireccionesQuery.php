<?php

namespace App\Application\Redirecciones\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetRedireccionesQuery
{
    public function __construct(public PaginationDTO $pagination) {}
}
