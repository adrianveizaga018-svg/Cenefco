<?php

namespace App\Application\Trivia\QueryHandlers;

use App\Application\Trivia\DTOs\TriviaCategoriaDTO;
use App\Application\Trivia\Queries\GetTriviaCategoriaBySlugQuery;
use App\Domain\Trivia\Contracts\TriviaCategoriaRepositoryInterface;

class GetTriviaCategoriaBySlugQueryHandler
{
    public function __construct(private readonly TriviaCategoriaRepositoryInterface $repository) {}

    public function handle(GetTriviaCategoriaBySlugQuery $query): TriviaCategoriaDTO
    {
        return $this->repository->findBySlug($query->slug);
    }
}
