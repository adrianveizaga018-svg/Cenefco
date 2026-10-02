<?php

namespace App\Application\Universidades\QueryHandlers;

use App\Application\Universidades\DTOs\UniversidadDTO;
use App\Application\Universidades\Queries\GetUniversidadByIdQuery;
use App\Domain\Universidades\Contracts\UniversidadRepositoryInterface;

class GetUniversidadByIdQueryHandler
{
    public function __construct(private readonly UniversidadRepositoryInterface $repository) {}

    public function handle(GetUniversidadByIdQuery $query): UniversidadDTO
    {
        return UniversidadDTO::fromRow($this->repository->findById($query->id));
    }
}
