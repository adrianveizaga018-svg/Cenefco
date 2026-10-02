<?php

namespace App\Application\Intents\Handlers;

use App\Application\Intents\Commands\UpdateIntentCommand;
use App\Application\Intents\DTOs\IntentDTO;
use App\Domain\Intents\Contracts\IntentRepositoryInterface;

class UpdateIntentHandler
{
    public function __construct(private readonly IntentRepositoryInterface $repository) {}

    public function handle(UpdateIntentCommand $command): IntentDTO
    {
        $this->repository->update($command->id, [
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

        return IntentDTO::fromRow($this->repository->findById($command->id));
    }
}
