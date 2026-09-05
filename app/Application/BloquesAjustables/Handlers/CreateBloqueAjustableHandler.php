<?php

namespace App\Application\BloquesAjustables\Handlers;

use App\Application\BloquesAjustables\Commands\CreateBloqueAjustableCommand;
use App\Application\BloquesAjustables\DTOs\BloqueAjustableDTO;
use App\Domain\BloquesAjustables\Contracts\BloqueAjustableRepositoryInterface;

class CreateBloqueAjustableHandler
{
    public function __construct(
        private readonly BloqueAjustableRepositoryInterface $repository,
    ) {}

    public function handle(CreateBloqueAjustableCommand $command): BloqueAjustableDTO
    {
        $row = $this->repository->create([
            'id_bloqueajustable' => $command->idBloqueajustable,
            'id_pagina'          => $command->idPagina,
            'id_bloqueplantilla' => $command->idBloqueplantilla,
            'titulo'             => $command->titulo,
            'id_us_reg'          => $command->idUsReg,
            'fecha_reg'          => now(),
            'estado'             => 1,
        ]);

        return BloqueAjustableDTO::fromRow($row);
    }
}
