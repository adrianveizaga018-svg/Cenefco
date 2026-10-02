<?php

namespace App\Application\Formularios\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetFormulariosQuery
{
    public function __construct(public PaginationDTO $pagination) {}
}
