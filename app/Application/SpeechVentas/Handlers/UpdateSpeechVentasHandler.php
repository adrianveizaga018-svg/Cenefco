<?php

namespace App\Application\SpeechVentas\Handlers;

use App\Application\SpeechVentas\Commands\UpdateSpeechVentasCommand;
use App\Application\SpeechVentas\DTOs\SpeechVentasDTO;
use App\Domain\SpeechVentas\Contracts\SpeechVentasRepositoryInterface;

class UpdateSpeechVentasHandler
{
    public function __construct(private readonly SpeechVentasRepositoryInterface $repository) {}

    public function handle(UpdateSpeechVentasCommand $command): SpeechVentasDTO
    {
        $this->repository->update($command->id, [
            'titulo'         => $command->titulo,
            'categoria'      => $command->categoria,
            'contenido'      => $command->contenido,
            'palabras_clave' => $command->palabrasClave,
            'activo'         => $command->activo,
            'orden'          => $command->orden,
        ]);

        return SpeechVentasDTO::fromRow($this->repository->findById($command->id));
    }
}
