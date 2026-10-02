<?php

namespace App\Application\Asesores\Handlers;

use App\Application\Asesores\Commands\UpdateAsesorCommand;
use App\Application\Asesores\DTOs\AsesorDTO;
use App\Domain\Asesores\Contracts\AsesorRepositoryInterface;

class UpdateAsesorHandler
{
    public function __construct(private readonly AsesorRepositoryInterface $repository) {}

    public function handle(UpdateAsesorCommand $command): AsesorDTO
    {
        $this->repository->update($command->id, array_filter([
            'nombre'       => $command->nombre,
            'telefono'     => $command->telefono,
            'email'        => $command->email,
            'especialidad' => $command->especialidad,
            'disponible'   => $command->disponible,
            'activo'       => $command->activo,
            'updated_at'   => now(),
        ], fn ($v) => $v !== null));

        return AsesorDTO::fromRow($this->repository->findById($command->id));
    }
}
