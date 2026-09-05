<?php

namespace App\Application\PreguntasFrecuentes\QueryHandlers;

use App\Application\PreguntasFrecuentes\Queries\GetPreguntasFrecuentesQuery;
use App\Domain\PreguntasFrecuentes\Contracts\PreguntaFrecuenteRepositoryInterface;

class GetPreguntasFrecuentesQueryHandler
{
    public function __construct(private readonly PreguntaFrecuenteRepositoryInterface $repository) {}

    public function handle(GetPreguntasFrecuentesQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->soloActivos);
    }
}
