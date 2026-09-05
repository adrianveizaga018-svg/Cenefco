<?php

namespace App\Application\TiposBanco\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetTiposBancoQuery
{
    public function __construct(public PaginationDTO $pagination) {}
}
