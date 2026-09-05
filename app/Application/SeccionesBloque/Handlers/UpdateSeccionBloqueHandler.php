<?php

namespace App\Application\SeccionesBloque\Handlers;

use App\Application\SeccionesBloque\Commands\UpdateSeccionBloqueCommand;
use App\Domain\SeccionesBloque\Contracts\SeccionBloqueRepositoryInterface;

class UpdateSeccionBloqueHandler
{
    public function __construct(
        private readonly SeccionBloqueRepositoryInterface $repository,
    ) {}

    public function handle(UpdateSeccionBloqueCommand $command): void
    {
        $this->repository->update($command->idSeccionbloque, array_filter([
            'id_bloqueajustable' => $command->idBloqueajustable,
            'titulo'             => $command->titulo,
            'contenido'          => $command->contenido,
        ], fn ($v) => $v !== null));
    }
}
