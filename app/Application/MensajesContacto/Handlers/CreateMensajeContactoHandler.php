<?php

namespace App\Application\MensajesContacto\Handlers;

use App\Application\MensajesContacto\Commands\CreateMensajeContactoCommand;
use App\Application\MensajesContacto\DTOs\MensajeContactoDTO;
use App\Domain\MensajesContacto\Contracts\MensajeContactoRepositoryInterface;

class CreateMensajeContactoHandler
{
    public function __construct(private readonly MensajeContactoRepositoryInterface $repository) {}

    public function handle(CreateMensajeContactoCommand $command): MensajeContactoDTO
    {
        return $this->repository->create([
            'nombre_remitente' => $command->nombre_remitente,
            'email_remitente' => $command->email_remitente,
            'telefono_remitente' => $command->telefono_remitente,
            'asunto' => $command->asunto,
            'mensaje' => $command->mensaje,
            'secretaria_destino_id' => $command->secretaria_destino_id,
            'ip_origen' => $command->ip_origen,
            'estado' => 'nuevo',
        ]);
    }
}
