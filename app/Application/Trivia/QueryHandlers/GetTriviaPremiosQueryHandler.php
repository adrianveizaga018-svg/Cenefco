<?php

namespace App\Application\Trivia\QueryHandlers;

use App\Application\Trivia\Queries\GetTriviaPremiosQuery;
use App\Domain\Trivia\Contracts\TriviaPremioRepositoryInterface;

class GetTriviaPremiosQueryHandler
{
    public function __construct(
        private readonly TriviaPremioRepositoryInterface $repository
    ) {}

    public function handle(GetTriviaPremiosQuery $query): array
    {
        return $this->repository->paginate($query->pagination);
    }
}
