<?php

namespace App\Application\HistoriaMunicipio\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetHistoriaMunicipioQuery
{
    public function __construct(public PaginationDTO $pagination) {}
}
