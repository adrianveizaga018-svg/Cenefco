<?php

namespace App\Application\SugerenciasReclamos\Handlers;

use App\Application\SugerenciasReclamos\Commands\RespondSugerenciaReclamoCommand;
use App\Application\SugerenciasReclamos\DTOs\SugerenciaReclamoDTO;
use App\Domain\SugerenciasReclamos\Contracts\SugerenciaReclamoRepositoryInterface;

class RespondSugerenciaReclamoHandler
{
    public function __construct(private readonly SugerenciaReclamoRepositoryInterface $repository) {}

    public function handle(RespondSugerenciaReclamoCommand $command): SugerenciaReclamoDTO
    {
        return $this->repository->update($command->id, [
            'respuesta' => $command->respuesta,
            'respondido_por' => $command->respondido_por,
            'respondido_at' => now(),
            'estado' => $command->estado,
        ]);
    }
}
