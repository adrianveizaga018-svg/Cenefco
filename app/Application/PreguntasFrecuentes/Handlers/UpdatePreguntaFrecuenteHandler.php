<?php

namespace App\Application\PreguntasFrecuentes\Handlers;

use App\Application\PreguntasFrecuentes\Commands\UpdatePreguntaFrecuenteCommand;
use App\Application\PreguntasFrecuentes\DTOs\PreguntaFrecuenteDTO;
use App\Domain\PreguntasFrecuentes\Contracts\PreguntaFrecuenteRepositoryInterface;

class UpdatePreguntaFrecuenteHandler
{
    public function __construct(private readonly PreguntaFrecuenteRepositoryInterface $repository) {}

    public function handle(UpdatePreguntaFrecuenteCommand $command): PreguntaFrecuenteDTO
    {
        $data = array_filter([
            'pregunta'  => $command->pregunta,
            'respuesta' => $command->respuesta,
            'categoria' => $command->categoria,
            'orden'     => $command->orden,
            'activo'    => $command->activo,
        ], fn ($v) => $v !== null);

        return $this->repository->update($command->id, $data);
    }
}
