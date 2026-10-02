<?php

namespace App\Application\MensajesContacto\Handlers;

use App\Application\MensajesContacto\Commands\DeleteMensajeContactoCommand;
use App\Domain\MensajesContacto\Contracts\MensajeContactoRepositoryInterface;

class DeleteMensajeContactoHandler
{
    public function __construct(private readonly MensajeContactoRepositoryInterface $repository) {}

    public function handle(DeleteMensajeContactoCommand $command): bool
    {
        return $this->repository->delete($command->ids);
    }
}
