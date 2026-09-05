<?php

namespace App\Application\Universidades\QueryHandlers;

use App\Application\Universidades\Queries\GetUniversidadesQuery;
use App\Domain\Universidades\Contracts\UniversidadRepositoryInterface;

class GetUniversidadesQueryHandler
{
    public function __construct(private readonly UniversidadRepositoryInterface $repository) {}

    public function handle(GetUniversidadesQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->query, $query->idCiudad, $query->idTipoUniversidad);
    }
}
