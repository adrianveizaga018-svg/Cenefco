<?php

namespace App\Application\Trivia\Handlers;

use App\Application\Trivia\Commands\DeleteTriviaPremioCommand;
use App\Domain\Trivia\Contracts\TriviaPremioRepositoryInterface;
use App\Domain\Trivia\Exceptions\TriviaPremioNotFoundException;

class DeleteTriviaPremioHandler
{
    public function __construct(
        private readonly TriviaPremioRepositoryInterface $repository
    ) {}

    public function handle(DeleteTriviaPremioCommand $command): void
    {
        if (! $this->repository->findById($command->id)) {
            throw new TriviaPremioNotFoundException($command->id);
        }

        $this->repository->delete($command->id);
    }
}
