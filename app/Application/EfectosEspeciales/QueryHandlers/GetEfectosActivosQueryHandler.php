<?php

namespace App\Application\EfectosEspeciales\QueryHandlers;

use App\Application\EfectosEspeciales\Queries\GetEfectosActivosQuery;
use App\Domain\EfectosEspeciales\Contracts\EfectoEspecialRepositoryInterface;

class GetEfectosActivosQueryHandler
{
    public function __construct(private readonly EfectoEspecialRepositoryInterface $repository) {}

    public function handle(GetEfectosActivosQuery $query): array
    {
        return $this->repository->getActivos();
    }
}
