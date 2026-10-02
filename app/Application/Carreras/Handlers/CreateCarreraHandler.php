<?php

namespace App\Application\Carreras\Handlers;

use App\Application\Carreras\Commands\CreateCarreraCommand;
use App\Application\Carreras\DTOs\CarreraDTO;
use App\Domain\Carreras\Contracts\CarreraRepositoryInterface;

class CreateCarreraHandler
{
    public function __construct(
        private readonly CarreraRepositoryInterface $repository
    ) {}

    public function handle(CreateCarreraCommand $command): CarreraDTO
    {
        return $this->repository->create([
            'id_carrera'    => $command->id_carrera,
            'id_us_reg'     => $command->id_us_reg ?? 0,
            'num_carrera'   => $command->num_carrera ?? 0,
            'nombre_carrera' => $command->nombre_carrera,
            'estado'        => $command->estado,
            'fecha_reg'     => now(),
        ]);
    }
}
