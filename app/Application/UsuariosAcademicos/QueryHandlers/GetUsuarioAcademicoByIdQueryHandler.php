<?php

namespace App\Application\UsuariosAcademicos\QueryHandlers;

use App\Application\UsuariosAcademicos\DTOs\UsuarioAcademicoDTO;
use App\Application\UsuariosAcademicos\Queries\GetUsuarioAcademicoByIdQuery;
use App\Domain\UsuariosAcademicos\Contracts\UsuarioAcademicoRepositoryInterface;

class GetUsuarioAcademicoByIdQueryHandler
{
    public function __construct(
        private readonly UsuarioAcademicoRepositoryInterface $repository
    ) {}

    public function handle(GetUsuarioAcademicoByIdQuery $query): UsuarioAcademicoDTO
    {
        return $this->repository->findById($query->id);
    }
}
