<?php

namespace App\Application\CategoriasNoticia\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetCategoriasNoticiaQuery
{
    public function __construct(public PaginationDTO $pagination) {}
}
