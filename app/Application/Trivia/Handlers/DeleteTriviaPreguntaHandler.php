<?php

namespace App\Application\Trivia\Handlers;

use App\Application\Trivia\Commands\DeleteTriviaPreguntaCommand;
use App\Domain\Trivia\Contracts\TriviaPreguntaRepositoryInterface;

class DeleteTriviaPreguntaHandler
{
    public function __construct(
        private readonly TriviaPreguntaRepositoryInterface $repository
    ) {}

    public function handle(DeleteTriviaPreguntaCommand $command): bool
    {
        return $this->repository->delete($command->id);
    }
}
