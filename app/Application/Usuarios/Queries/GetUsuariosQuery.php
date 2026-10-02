<?php

namespace App\Application\Usuarios\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetUsuariosQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public array $filters = [],
    ) {}
}
