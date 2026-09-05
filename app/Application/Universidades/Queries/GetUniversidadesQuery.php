<?php

namespace App\Application\Universidades\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetUniversidadesQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?string       $query,
        public ?int          $idCiudad,
        public ?int          $idTipoUniversidad,
    ) {}
}
