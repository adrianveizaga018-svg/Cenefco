<?php

namespace App\Application\SugerenciasReclamos\QueryHandlers;

use App\Application\SugerenciasReclamos\DTOs\SugerenciaReclamoDTO;
use App\Application\SugerenciasReclamos\Queries\GetSugerenciaReclamoByIdQuery;
use App\Domain\SugerenciasReclamos\Contracts\SugerenciaReclamoRepositoryInterface;

class GetSugerenciaReclamoByIdQueryHandler
{
    public function __construct(private readonly SugerenciaReclamoRepositoryInterface $repository) {}

    public function handle(GetSugerenciaReclamoByIdQuery $query): SugerenciaReclamoDTO
    {
        return $this->repository->findById($query->id);
    }
}
