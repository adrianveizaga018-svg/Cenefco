<?php

namespace App\Application\TiposEvento\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetTiposEventoQuery
{
    public function __construct(public PaginationDTO $pagination) {}
}
