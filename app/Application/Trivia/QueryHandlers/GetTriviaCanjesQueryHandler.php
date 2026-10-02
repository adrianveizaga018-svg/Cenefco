<?php

namespace App\Application\Trivia\QueryHandlers;

use App\Application\Trivia\Queries\GetTriviaCanjesQuery;
use App\Domain\Trivia\Contracts\TriviaCanjeRepositoryInterface;

class GetTriviaCanjesQueryHandler
{
    public function __construct(
        private readonly TriviaCanjeRepositoryInterface $repository
    ) {}

    public function handle(GetTriviaCanjesQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->filters);
    }
}
