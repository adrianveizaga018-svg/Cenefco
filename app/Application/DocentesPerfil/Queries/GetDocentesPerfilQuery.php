<?php

namespace App\Application\DocentesPerfil\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetDocentesPerfilQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public bool          $soloVisibles = false,
    ) {}
}
