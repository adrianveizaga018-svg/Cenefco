<?php

namespace App\Application\UsuariosAcademicos\QueryHandlers;

use App\Application\UsuariosAcademicos\Queries\GetUsuariosAcademicosQuery;
use App\Domain\UsuariosAcademicos\Contracts\UsuarioAcademicoRepositoryInterface;

class GetUsuariosAcademicosQueryHandler
{
    public function __construct(
        private readonly UsuarioAcademicoRepositoryInterface $repository
    ) {}

    public function handle(GetUsuariosAcademicosQuery $query): array
    {
        return $this->repository->paginate(
            $query->pagination,
            $query->conInactivos,
            $query->tipoestudiante,
            $query->idNiv,
        );
    }
}
