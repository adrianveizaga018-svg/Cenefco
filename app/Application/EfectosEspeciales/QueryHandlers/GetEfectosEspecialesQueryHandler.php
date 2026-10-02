<?php

namespace App\Application\EfectosEspeciales\QueryHandlers;

use App\Application\EfectosEspeciales\Queries\GetEfectosEspecialesQuery;
use App\Domain\EfectosEspeciales\Contracts\EfectoEspecialRepositoryInterface;

class GetEfectosEspecialesQueryHandler
{
    public function __construct(private readonly EfectoEspecialRepositoryInterface $repository) {}

    public function handle(GetEfectosEspecialesQuery $query): array
    {
        return $this->repository->paginate($query->pagination);
    }
}
