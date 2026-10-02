<?php

namespace App\Application\UsuariosMoodle\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetUsuariosMoodleQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?int $idUs,
        public ?int $idMoodle,
        public bool $conInactivos,
    ) {}
}
