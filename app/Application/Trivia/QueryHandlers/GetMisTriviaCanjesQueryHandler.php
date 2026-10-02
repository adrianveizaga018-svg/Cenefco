<?php

namespace App\Application\Trivia\QueryHandlers;

use App\Application\Trivia\DTOs\TriviaCanjeUsuarioDTO;
use App\Application\Trivia\Queries\GetMisTriviaCanjesQuery;
use App\Domain\Trivia\Contracts\TriviaCanjeRepositoryInterface;

class GetMisTriviaCanjesQueryHandler
{
    public function __construct(
        private readonly TriviaCanjeRepositoryInterface $repository
    ) {}

    public function handle(GetMisTriviaCanjesQuery $query): array
    {
        return array_map(
            fn ($model) => TriviaCanjeUsuarioDTO::fromModel($model),
            $this->repository->misCanjes($query->usuarioId)
        );
    }
}
