<?php

namespace App\Application\Trivia\QueryHandlers;

use App\Application\Trivia\DTOs\TriviaCategoriaDTO;
use App\Application\Trivia\Queries\GetTriviaCategoriaByIdQuery;
use App\Domain\Trivia\Contracts\TriviaCategoriaRepositoryInterface;

class GetTriviaCategoriaByIdQueryHandler
{
    public function __construct(private readonly TriviaCategoriaRepositoryInterface $repository) {}

    public function handle(GetTriviaCategoriaByIdQuery $query): TriviaCategoriaDTO
    {
        return $this->repository->findById($query->id);
    }
}
