<?php

namespace App\Application\DocentesPerfil\QueryHandlers;

use App\Application\DocentesPerfil\Queries\GetReporteMateriasQuery;
use App\Domain\DocentesPerfil\Contracts\DocentePerfilRepositoryInterface;

class GetReporteMateriasQueryHandler
{
    public function __construct(private readonly DocentePerfilRepositoryInterface $repository) {}

    public function handle(GetReporteMateriasQuery $q): array
    {
        return $this->repository->reporteMaterias($q->gestion, $q->periodo);
    }
}
