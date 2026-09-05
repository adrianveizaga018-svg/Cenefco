<?php

namespace App\Application\SeccionesBloque\Handlers;

use App\Application\SeccionesBloque\Commands\CreateSeccionBloqueCommand;
use App\Application\SeccionesBloque\DTOs\SeccionBloqueDTO;
use App\Domain\SeccionesBloque\Contracts\SeccionBloqueRepositoryInterface;

class CreateSeccionBloqueHandler
{
    public function __construct(
        private readonly SeccionBloqueRepositoryInterface $repository,
    ) {}

    public function handle(CreateSeccionBloqueCommand $command): SeccionBloqueDTO
    {
        $row = $this->repository->create([
            'id_seccionbloque'   => $command->idSeccionbloque,
            'id_bloqueajustable' => $command->idBloqueajustable,
            'titulo'             => $command->titulo,
            'contenido'          => $command->contenido,
            'id_us_reg'          => $command->idUsReg,
            'fecha_reg'          => now(),
            'estado'             => 1,
        ]);

        return SeccionBloqueDTO::fromRow($row);
    }
}
