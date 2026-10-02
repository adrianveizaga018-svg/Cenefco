<?php

namespace App\Application\PreguntasFrecuentes\QueryHandlers;

use App\Application\PreguntasFrecuentes\Queries\GetPreguntaFrecuenteByIdQuery;
use App\Domain\PreguntasFrecuentes\Contracts\PreguntaFrecuenteRepositoryInterface;

class GetPreguntaFrecuenteByIdQueryHandler
{
    public function __construct(private readonly PreguntaFrecuenteRepositoryInterface $repository) {}

    public function handle(GetPreguntaFrecuenteByIdQuery $query): mixed
    {
        return $this->repository->findById($query->id);
    }
}
