<?php

namespace App\Application\HistoriaMunicipio\QueryHandlers;

use App\Application\HistoriaMunicipio\Queries\GetHistoriaMunicipioByIdQuery;
use App\Domain\HistoriaMunicipio\Contracts\HistoriaMunicipioRepositoryInterface;

class GetHistoriaMunicipioByIdQueryHandler
{
    public function __construct(private readonly HistoriaMunicipioRepositoryInterface $repository) {}

    public function handle(GetHistoriaMunicipioByIdQuery $query): mixed
    {
        return $this->repository->findById($query->id);
    }
}
