<?php

namespace App\Application\Intents\Handlers;

use App\Application\Intents\Commands\CreateIntentCommand;
use App\Application\Intents\DTOs\IntentDTO;
use App\Domain\Intents\Contracts\IntentRepositoryInterface;

class CreateIntentHandler
{
    public function __construct(private readonly IntentRepositoryInterface $repository) {}

    public function handle(CreateIntentCommand $command): IntentDTO
    {
        $row = $this->repository->create([
            'nombre'               => $command->nombre,
            'slug'                 => $command->slug,
            'dominio'              => $command->dominio,
            'prioridad'            => $command->prioridad,
            'accion'               => $command->accion,
            'activo'               => $command->activo,
            'orden'                => $command->orden,
            'eventos'              => $command->eventos,
            'input_contexts'       => $command->inputContexts,
            'output_contexts'      => $command->outputContexts,
            'frases_entrenamiento' => $command->frasesEntrenamiento,
            'respuestas'           => $command->respuestas,
        ]);

        return IntentDTO::fromRow($row);
    }
}
