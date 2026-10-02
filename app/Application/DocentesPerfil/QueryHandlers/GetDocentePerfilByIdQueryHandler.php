<?php

namespace App\Application\DocentesPerfil\QueryHandlers;

use App\Application\DocentesPerfil\DTOs\DocentePerfilDTO;
use App\Application\DocentesPerfil\Queries\GetDocentePerfilByIdQuery;
use App\Domain\DocentesPerfil\Contracts\DocentePerfilRepositoryInterface;

class GetDocentePerfilByIdQueryHandler
{
    public function __construct(private readonly DocentePerfilRepositoryInterface $repository) {}

    public function handle(GetDocentePerfilByIdQuery $q): DocentePerfilDTO
    {
        return $this->repository->findById($q->id);
    }
}
