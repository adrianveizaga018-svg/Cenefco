<?php

namespace App\Application\EfectosEspeciales\QueryHandlers;

use App\Application\EfectosEspeciales\DTOs\EfectoEspecialDTO;
use App\Application\EfectosEspeciales\Queries\GetEfectoEspecialByIdQuery;
use App\Domain\EfectosEspeciales\Contracts\EfectoEspecialRepositoryInterface;

class GetEfectoEspecialByIdQueryHandler
{
    public function __construct(private readonly EfectoEspecialRepositoryInterface $repository) {}

    public function handle(GetEfectoEspecialByIdQuery $query): EfectoEspecialDTO
    {
        return $this->repository->findById($query->id);
    }
}
