<?php

namespace App\Application\CitasAsesoria\Handlers;

use App\Application\CitasAsesoria\Commands\UpdateCitaAsesoriaCommand;
use App\Domain\CitasAsesoria\Contracts\CitaAsesoriaRepositoryInterface;

class UpdateCitaAsesoriaHandler
{
    public function __construct(
        private readonly CitaAsesoriaRepositoryInterface $repository,
    ) {}

    public function handle(UpdateCitaAsesoriaCommand $command): void
    {
        $this->repository->update($command->id, array_filter([
            'nombre'   => $command->nombre,
            'email'    => $command->email,
            'telefono' => $command->telefono,
            'fecha'    => $command->fecha,
            'estado'   => $command->estado,
        ], fn ($v) => $v !== null));
    }
}
