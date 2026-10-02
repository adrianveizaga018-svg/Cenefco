<?php

namespace App\Application\SugerenciasReclamos\QueryHandlers;

use App\Application\SugerenciasReclamos\Queries\GetSugerenciasReclamosQuery;
use App\Domain\SugerenciasReclamos\Contracts\SugerenciaReclamoRepositoryInterface;

class GetSugerenciasReclamosQueryHandler
{
    public function __construct(private readonly SugerenciaReclamoRepositoryInterface $repository) {}

    public function handle(GetSugerenciasReclamosQuery $query): array
    {
        return $this->repository->paginate($query->pagination);
    }
}
