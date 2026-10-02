<?php

namespace App\Application\UsuariosPrograma\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetUsuariosProgramaQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?int $idUs,
        public ?int $idPrograma,
        public ?int $idTipoPrograma,
        public bool $conInactivos,
    ) {}
}
