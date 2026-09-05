<?php

namespace App\Application\BloquesPlantilla\Handlers;

use App\Application\BloquesPlantilla\Commands\CreateBloquePlantillaCommand;
use App\Application\BloquesPlantilla\DTOs\BloquePlantillaDTO;
use App\Domain\BloquesPlantilla\Contracts\BloquePlantillaRepositoryInterface;

class CreateBloquePlantillaHandler
{
    public function __construct(
        private readonly BloquePlantillaRepositoryInterface $repository,
    ) {}

    public function handle(CreateBloquePlantillaCommand $command): BloquePlantillaDTO
    {
        $row = $this->repository->create([
            'id_bloqueplantilla' => $command->idBloqueplantilla,
            'nombre'             => $command->nombre,
            'descripcion'        => $command->descripcion,
            'id_us_reg'          => $command->idUsReg,
            'fecha_reg'          => now(),
            'estado'             => 1,
        ]);

        return BloquePlantillaDTO::fromRow($row);
    }
}
