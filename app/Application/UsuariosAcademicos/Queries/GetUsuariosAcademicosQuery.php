<?php

namespace App\Application\UsuariosAcademicos\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetUsuariosAcademicosQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public bool $conInactivos = false,
        public ?string $tipoestudiante = null,
        public ?int $idNiv = null,
    ) {}
}
