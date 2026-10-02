<?php

namespace App\Application\Trivia\QueryHandlers;

use App\Application\Trivia\Queries\GetTriviaNivelesQuery;
use App\Domain\Trivia\Contracts\TriviaNivelRepositoryInterface;

class GetTriviaNivelesQueryHandler
{
    public function __construct(private readonly TriviaNivelRepositoryInterface $repository) {}

    public function handle(GetTriviaNivelesQuery $query): array
    {
        return $this->repository->findByCategoria($query->categoriaId, $query->soloActivos);
    }
}
