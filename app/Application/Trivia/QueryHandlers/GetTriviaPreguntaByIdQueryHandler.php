<?php

namespace App\Application\Trivia\QueryHandlers;

use App\Application\Trivia\DTOs\TriviaPreguntaDTO;
use App\Application\Trivia\Queries\GetTriviaPreguntaByIdQuery;
use App\Domain\Trivia\Contracts\TriviaPreguntaRepositoryInterface;

class GetTriviaPreguntaByIdQueryHandler
{
    public function __construct(private readonly TriviaPreguntaRepositoryInterface $repository) {}

    public function handle(GetTriviaPreguntaByIdQuery $query): TriviaPreguntaDTO
    {
        return $this->repository->findById($query->id);
    }
}
