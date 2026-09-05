<?php

namespace App\Application\Trivia\QueryHandlers;

use App\Application\Trivia\DTOs\TriviaNivelDTO;
use App\Application\Trivia\Queries\GetTriviaNivelByIdQuery;
use App\Domain\Trivia\Contracts\TriviaNivelRepositoryInterface;

class GetTriviaNivelByIdQueryHandler
{
    public function __construct(private readonly TriviaNivelRepositoryInterface $repository) {}

    public function handle(GetTriviaNivelByIdQuery $query): TriviaNivelDTO
    {
        return $this->repository->findById($query->id);
    }
}
