<?php

namespace App\Application\Trivia\QueryHandlers;

use App\Application\Trivia\DTOs\TriviaPremioDTO;
use App\Application\Trivia\Queries\GetTriviaPremiosActivosQuery;
use App\Domain\Trivia\Contracts\TriviaPremioRepositoryInterface;

class GetTriviaPremiosActivosQueryHandler
{
    public function __construct(
        private readonly TriviaPremioRepositoryInterface $repository
    ) {}

    public function handle(GetTriviaPremiosActivosQuery $query): array
    {
        return array_map(
            fn ($model) => TriviaPremioDTO::fromModel($model),
            $this->repository->activos()
        );
    }
}
