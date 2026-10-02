<?php

namespace App\Application\UsuariosTipoPrograma\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetUsuariosTipoProgramaQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?int $idUs,
        public ?int $idTipoPrograma,
        public bool $conInactivos,
    ) {}
}
