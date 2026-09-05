<?php

namespace App\Application\DocentesPerfil\QueryHandlers;

use App\Application\DocentesPerfil\Queries\GetDocentesPerfilQuery;
use App\Domain\DocentesPerfil\Contracts\DocentePerfilRepositoryInterface;

class GetDocentesPerfilQueryHandler
{
    public function __construct(private readonly DocentePerfilRepositoryInterface $repository) {}

    public function handle(GetDocentesPerfilQuery $q): array
    {
        return $this->repository->paginate($q->pagination, $q->soloVisibles);
    }
}
