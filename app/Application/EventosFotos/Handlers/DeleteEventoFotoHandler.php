<?php

namespace App\Application\EventosFotos\Handlers;

use App\Application\EventosFotos\Commands\DeleteEventoFotoCommand;
use App\Domain\EventosFotos\Contracts\EventoFotoRepositoryInterface;

class DeleteEventoFotoHandler
{
    public function __construct(private readonly EventoFotoRepositoryInterface $repository) {}

    public function handle(DeleteEventoFotoCommand $command): bool
    {
        return $this->repository->delete($command->id);
    }
}
