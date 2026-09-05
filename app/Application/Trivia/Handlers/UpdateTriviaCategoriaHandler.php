<?php

namespace App\Application\Trivia\Handlers;

use App\Application\Trivia\Commands\UpdateTriviaCategoriaCommand;
use App\Application\Trivia\DTOs\TriviaCategoriaDTO;
use App\Domain\Trivia\Contracts\TriviaCategoriaRepositoryInterface;

class UpdateTriviaCategoriaHandler
{
    public function __construct(
        private readonly TriviaCategoriaRepositoryInterface $repository
    ) {}

    public function handle(UpdateTriviaCategoriaCommand $command): TriviaCategoriaDTO
    {
        return $this->repository->update($command->id, $command->data);
    }
}
