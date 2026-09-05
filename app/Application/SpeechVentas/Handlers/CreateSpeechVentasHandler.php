<?php

namespace App\Application\SpeechVentas\Handlers;

use App\Application\SpeechVentas\Commands\CreateSpeechVentasCommand;
use App\Application\SpeechVentas\DTOs\SpeechVentasDTO;
use App\Domain\SpeechVentas\Contracts\SpeechVentasRepositoryInterface;

class CreateSpeechVentasHandler
{
    public function __construct(private readonly SpeechVentasRepositoryInterface $repository) {}

    public function handle(CreateSpeechVentasCommand $command): SpeechVentasDTO
    {
        $row = $this->repository->create([
            'titulo'         => $command->titulo,
            'categoria'      => $command->categoria,
            'contenido'      => $command->contenido,
            'palabras_clave' => $command->palabrasClave,
            'activo'         => $command->activo,
            'orden'          => $command->orden,
        ]);

        return SpeechVentasDTO::fromRow($row);
    }
}
