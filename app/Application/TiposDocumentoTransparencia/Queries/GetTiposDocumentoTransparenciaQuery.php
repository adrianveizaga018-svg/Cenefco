<?php

namespace App\Application\TiposDocumentoTransparencia\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetTiposDocumentoTransparenciaQuery
{
    public function __construct(public PaginationDTO $pagination) {}
}
