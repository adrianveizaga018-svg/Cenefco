<?php

namespace App\Application\Intents\Handlers;

use App\Application\Intents\Commands\DeleteIntentCommand;
use App\Domain\Intents\Contracts\IntentRepositoryInterface;

class DeleteIntentHandler
{
    public function __construct(private readonly IntentRepositoryInterface $repository) {}

    public function handle(DeleteIntentCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
