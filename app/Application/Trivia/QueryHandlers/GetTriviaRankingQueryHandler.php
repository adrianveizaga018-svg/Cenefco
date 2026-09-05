<?php

namespace App\Application\Trivia\QueryHandlers;

use App\Application\Trivia\Queries\GetTriviaRankingQuery;
use App\Domain\Trivia\Contracts\TriviaPartidaRepositoryInterface;

class GetTriviaRankingQueryHandler
{
    public function __construct(private readonly TriviaPartidaRepositoryInterface $repository) {}

    public function handle(GetTriviaRankingQuery $query): array
    {
        return $this->repository->topJugadores($query->limite);
    }
}
