<?php

namespace App\Application\CampanasPublicidad\QueryHandlers;

use App\Application\CampanasPublicidad\Queries\GetCampanasPublicidadQuery;
use App\Domain\CampanasPublicidad\Contracts\CampanaPublicidadRepositoryInterface;

class GetCampanasPublicidadQueryHandler
{
    public function __construct(private readonly CampanaPublicidadRepositoryInterface $repository) {}

    public function handle(GetCampanasPublicidadQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->filtros);
    }
}
