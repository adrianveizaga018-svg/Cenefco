<?php

namespace App\Application\TiposNorma\Queries;

use App\Shared\Kernel\Contracts\QueryInterface;
use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetTiposNormaQuery implements QueryInterface
{
    public function __construct(public PaginationDTO $pagination) {}
}
