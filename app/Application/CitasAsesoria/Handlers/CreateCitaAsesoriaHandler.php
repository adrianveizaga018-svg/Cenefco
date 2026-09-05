<?php

namespace App\Application\CitasAsesoria\Handlers;

use App\Application\CitasAsesoria\Commands\CreateCitaAsesoriaCommand;
use App\Application\CitasAsesoria\DTOs\CitaAsesoriaDTO;
use App\Domain\CitasAsesoria\Contracts\CitaAsesoriaRepositoryInterface;

class CreateCitaAsesoriaHandler
{
    public function __construct(
        private readonly CitaAsesoriaRepositoryInterface $repository,
    ) {}

    public function handle(CreateCitaAsesoriaCommand $command): CitaAsesoriaDTO
    {
        $row = $this->repository->create([
            'nombre'   => $command->nombre,
            'email'    => $command->email,
            'telefono' => $command->telefono,
            'fecha'    => $command->fecha,
            'estado'   => $command->estado,
        ]);

        return CitaAsesoriaDTO::fromRow($row);
    }
}
