<?php

namespace App\Application\PreguntasFrecuentes\Handlers;

use App\Application\PreguntasFrecuentes\Commands\DeletePreguntaFrecuenteCommand;
use App\Domain\PreguntasFrecuentes\Contracts\PreguntaFrecuenteRepositoryInterface;

class DeletePreguntaFrecuenteHandler
{
    public function __construct(private readonly PreguntaFrecuenteRepositoryInterface $repository) {}

    public function handle(DeletePreguntaFrecuenteCommand $command): bool
    {
        return $this->repository->delete($command->ids);
    }
}
