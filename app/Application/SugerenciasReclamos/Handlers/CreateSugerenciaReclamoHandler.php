<?php

namespace App\Application\SugerenciasReclamos\Handlers;

use App\Application\SugerenciasReclamos\Commands\CreateSugerenciaReclamoCommand;
use App\Application\SugerenciasReclamos\DTOs\SugerenciaReclamoDTO;
use App\Domain\SugerenciasReclamos\Contracts\SugerenciaReclamoRepositoryInterface;

class CreateSugerenciaReclamoHandler
{
    public function __construct(private readonly SugerenciaReclamoRepositoryInterface $repository) {}

    public function handle(CreateSugerenciaReclamoCommand $command): SugerenciaReclamoDTO
    {
        return $this->repository->create([
            'usuario_id' => $command->usuario_id,
            'asunto' => $command->asunto,
            'mensaje' => $command->mensaje,
            'email_respuesta' => $command->email_respuesta,
            'secretaria_destino_id' => $command->secretaria_destino_id,
            'estado' => 'recibido',
        ]);
    }
}
