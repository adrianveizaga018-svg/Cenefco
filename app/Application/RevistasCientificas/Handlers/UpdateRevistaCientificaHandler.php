<?php

namespace App\Application\RevistasCientificas\Handlers;

use App\Application\RevistasCientificas\Commands\UpdateRevistaCientificaCommand;
use App\Application\RevistasCientificas\DTOs\RevistaCientificaDTO;
use App\Domain\RevistasCientificas\Contracts\RevistaCientificaRepositoryInterface;

class UpdateRevistaCientificaHandler
{
    public function __construct(private readonly RevistaCientificaRepositoryInterface $repository) {}

    public function handle(UpdateRevistaCientificaCommand $command): RevistaCientificaDTO
    {
        $changes = array_filter([
            'titulo_revistacientifica'      => $command->titulo_revistacientifica,
            'descripcion_revistacientifica' => $command->descripcion_revistacientifica,
            'fecha_publicacion'             => $command->fecha_publicacion,
            'archivo'                       => $command->archivo,
            'estado'                        => $command->estado,
        ], fn ($v) => $v !== null);

        $this->repository->update($command->id, $changes);

        return RevistaCientificaDTO::fromRow($this->repository->findById($command->id));
    }
}
