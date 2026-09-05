<?php

namespace App\Application\Asesores\Handlers;

use App\Application\Asesores\Commands\CreateAsesorCommand;
use App\Application\Asesores\DTOs\AsesorDTO;
use App\Domain\Asesores\Contracts\AsesorRepositoryInterface;

class CreateAsesorHandler
{
    public function __construct(private readonly AsesorRepositoryInterface $repository) {}

    public function handle(CreateAsesorCommand $command): AsesorDTO
    {
        $row = $this->repository->create([
            'nombre'       => $command->nombre,
            'telefono'     => $command->telefono,
            'email'        => $command->email,
            'especialidad' => $command->especialidad,
            'disponible'   => $command->disponible,
            'activo'       => $command->activo,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        return AsesorDTO::fromRow($row);
    }
}
