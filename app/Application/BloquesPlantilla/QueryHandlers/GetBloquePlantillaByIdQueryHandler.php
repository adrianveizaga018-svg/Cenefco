<?php

namespace App\Application\BloquesPlantilla\QueryHandlers;

use App\Application\BloquesPlantilla\DTOs\BloquePlantillaDTO;
use App\Application\BloquesPlantilla\Queries\GetBloquePlantillaByIdQuery;
use App\Domain\BloquesPlantilla\Contracts\BloquePlantillaRepositoryInterface;

class GetBloquePlantillaByIdQueryHandler
{
    public function __construct(
        private readonly BloquePlantillaRepositoryInterface $repository,
    ) {}

    public function handle(GetBloquePlantillaByIdQuery $query): BloquePlantillaDTO
    {
        return BloquePlantillaDTO::fromRow($this->repository->findById($query->idBloqueplantilla));
    }
}
