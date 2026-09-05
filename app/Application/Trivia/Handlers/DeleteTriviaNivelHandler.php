<?php

namespace App\Application\Trivia\Handlers;

use App\Application\Trivia\Commands\DeleteTriviaNivelCommand;
use App\Domain\Trivia\Contracts\TriviaNivelRepositoryInterface;

class DeleteTriviaNivelHandler
{
    public function __construct(
        private readonly TriviaNivelRepositoryInterface $repository
    ) {}

    public function handle(DeleteTriviaNivelCommand $command): bool
    {
        return $this->repository->delete($command->id);
    }
}
