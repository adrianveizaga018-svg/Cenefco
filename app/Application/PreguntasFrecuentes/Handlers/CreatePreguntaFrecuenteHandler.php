<?php

namespace App\Application\PreguntasFrecuentes\Handlers;

use App\Application\PreguntasFrecuentes\Commands\CreatePreguntaFrecuenteCommand;
use App\Application\PreguntasFrecuentes\DTOs\PreguntaFrecuenteDTO;
use App\Domain\PreguntasFrecuentes\Contracts\PreguntaFrecuenteRepositoryInterface;

class CreatePreguntaFrecuenteHandler
{
    public function __construct(private readonly PreguntaFrecuenteRepositoryInterface $repository) {}

    public function handle(CreatePreguntaFrecuenteCommand $command): PreguntaFrecuenteDTO
    {
        return $this->repository->create([
            'pregunta' => $command->pregunta,
            'respuesta' => $command->respuesta,
            'categoria' => $command->categoria,
            'orden' => $command->orden,
            'activo' => $command->activo,
        ]);
    }
}
