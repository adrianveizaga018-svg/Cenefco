<?php

namespace App\Application\BloquesAjustables\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetBloquesAjustablesQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?int $idPagina,
        public ?int $idBloqueplantilla,
        public bool $conInactivos,
    ) {}
}
