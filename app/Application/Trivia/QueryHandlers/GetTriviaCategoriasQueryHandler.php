<?php

namespace App\Application\Trivia\QueryHandlers;

use App\Application\Trivia\Queries\GetTriviaCategoriasQuery;
use App\Domain\Trivia\Contracts\TriviaCategoriaRepositoryInterface;

class GetTriviaCategoriasQueryHandler
{
    public function __construct(private readonly TriviaCategoriaRepositoryInterface $repository) {}

    public function handle(GetTriviaCategoriasQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->soloActivos);
    }
}
