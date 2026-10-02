<?php

namespace App\Application\UsuariosPrograma\QueryHandlers;

use App\Application\UsuariosPrograma\Queries\GetUsuariosProgramaQuery;
use App\Domain\UsuariosPrograma\Contracts\UsuarioProgramaRepositoryInterface;

class GetUsuariosProgramaQueryHandler
{
    public function __construct(
        private readonly UsuarioProgramaRepositoryInterface $repository,
    ) {}

    public function handle(GetUsuariosProgramaQuery $query): array
    {
        return $this->repository->paginate(
            $query->pagination,
            $query->idUs,
            $query->idPrograma,
            $query->idTipoPrograma,
            $query->conInactivos,
        );
    }
}
