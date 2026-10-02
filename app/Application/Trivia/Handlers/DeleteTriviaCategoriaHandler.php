<?php

namespace App\Application\Trivia\Handlers;

use App\Application\Trivia\Commands\DeleteTriviaCategoriaCommand;
use App\Domain\Trivia\Contracts\TriviaCategoriaRepositoryInterface;

class DeleteTriviaCategoriaHandler
{
    public function __construct(
        private readonly TriviaCategoriaRepositoryInterface $repository
    ) {}

    public function handle(DeleteTriviaCategoriaCommand $command): bool
    {
        return $this->repository->delete($command->id);
    }
}
