<?php

namespace App\Application\MensajesContacto\Handlers;

use App\Application\MensajesContacto\Commands\RespondMensajeContactoCommand;
use App\Application\MensajesContacto\DTOs\MensajeContactoDTO;
use App\Domain\MensajesContacto\Contracts\MensajeContactoRepositoryInterface;

class RespondMensajeContactoHandler
{
    public function __construct(private readonly MensajeContactoRepositoryInterface $repository) {}

    public function handle(RespondMensajeContactoCommand $command): MensajeContactoDTO
    {
        return $this->repository->update($command->id, [
            'respuesta' => $command->respuesta,
            'respondido_por' => $command->respondido_por,
            'respondido_at' => now(),
            'estado' => $command->estado,
        ]);
    }
}
