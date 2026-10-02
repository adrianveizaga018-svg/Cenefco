<?php

namespace App\Application\CampanasPublicidad\QueryHandlers;

use App\Application\CampanasPublicidad\DTOs\CampanaPublicidadDTO;
use App\Application\CampanasPublicidad\Queries\GetCampanaByIdQuery;
use App\Domain\CampanasPublicidad\Contracts\CampanaPublicidadRepositoryInterface;

class GetCampanaByIdQueryHandler
{
    public function __construct(private readonly CampanaPublicidadRepositoryInterface $repository) {}

    public function handle(GetCampanaByIdQuery $query): CampanaPublicidadDTO
    {
        return $this->repository->findById($query->id);
    }
}
