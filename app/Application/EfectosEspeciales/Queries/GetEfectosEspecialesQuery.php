<?php

namespace App\Application\EfectosEspeciales\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetEfectosEspecialesQuery
{
    public function __construct(public PaginationDTO $pagination) {}
}
