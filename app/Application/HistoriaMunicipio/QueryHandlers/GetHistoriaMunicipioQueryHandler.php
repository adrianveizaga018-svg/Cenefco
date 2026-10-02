<?php

namespace App\Application\HistoriaMunicipio\QueryHandlers;

use App\Application\HistoriaMunicipio\Queries\GetHistoriaMunicipioQuery;
use App\Domain\HistoriaMunicipio\Contracts\HistoriaMunicipioRepositoryInterface;

class GetHistoriaMunicipioQueryHandler
{
    public function __construct(private readonly HistoriaMunicipioRepositoryInterface $repository) {}

    public function handle(GetHistoriaMunicipioQuery $query): array
    {
        return $this->repository->paginate($query->pagination);
    }
}
