<?php

namespace App\Application\Trivia\Handlers;

use App\Application\Trivia\Commands\UpdateTriviaNivelCommand;
use App\Application\Trivia\DTOs\TriviaNivelDTO;
use App\Domain\Trivia\Contracts\TriviaNivelRepositoryInterface;

class UpdateTriviaNivelHandler
{
    public function __construct(
        private readonly TriviaNivelRepositoryInterface $repository
    ) {}

    public function handle(UpdateTriviaNivelCommand $command): TriviaNivelDTO
    {
        return $this->repository->update($command->id, $command->data);
    }
}
