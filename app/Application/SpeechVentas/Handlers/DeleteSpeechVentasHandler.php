<?php

namespace App\Application\SpeechVentas\Handlers;

use App\Application\SpeechVentas\Commands\DeleteSpeechVentasCommand;
use App\Domain\SpeechVentas\Contracts\SpeechVentasRepositoryInterface;

class DeleteSpeechVentasHandler
{
    public function __construct(private readonly SpeechVentasRepositoryInterface $repository) {}

    public function handle(DeleteSpeechVentasCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
