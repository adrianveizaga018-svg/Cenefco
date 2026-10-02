<?php

namespace App\Application\BloquesAjustables\Handlers;

use App\Application\BloquesAjustables\Commands\UpdateBloqueAjustableCommand;
use App\Domain\BloquesAjustables\Contracts\BloqueAjustableRepositoryInterface;

class UpdateBloqueAjustableHandler
{
    public function __construct(
        private readonly BloqueAjustableRepositoryInterface $repository,
    ) {}

    public function handle(UpdateBloqueAjustableCommand $command): void
    {
        $this->repository->update($command->idBloqueajustable, array_filter([
            'id_pagina'          => $command->idPagina,
            'id_bloqueplantilla' => $command->idBloqueplantilla,
            'titulo'             => $command->titulo,
        ], fn ($v) => $v !== null));
    }
}
