<?php

namespace App\Application\CertPlantillas\QueryHandlers;

use App\Application\CertPlantillas\Queries\GetCertPlantillasQuery;
use App\Domain\CertPlantillas\Contracts\CertPlantillaRepositoryInterface;

class GetCertPlantillasQueryHandler
{
    public function __construct(
        private readonly CertPlantillaRepositoryInterface $repository
    ) {}

    public function handle(GetCertPlantillasQuery $query): array
    {
        return $this->repository->paginate(
            $query->pagination,
            $query->tipo,
            $query->soloActivos,
        );
    }
}
