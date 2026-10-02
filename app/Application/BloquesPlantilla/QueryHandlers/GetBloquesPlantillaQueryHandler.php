<?php

namespace App\Application\BloquesPlantilla\QueryHandlers;

use App\Application\BloquesPlantilla\Queries\GetBloquesPlantillaQuery;
use App\Domain\BloquesPlantilla\Contracts\BloquePlantillaRepositoryInterface;

class GetBloquesPlantillaQueryHandler
{
    public function __construct(
        private readonly BloquePlantillaRepositoryInterface $repository,
    ) {}

    public function handle(GetBloquesPlantillaQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->conInactivos);
    }
}
