<?php

namespace App\Application\DirectorioInstitucional\Handlers;

use App\Application\DirectorioInstitucional\Commands\CreateDirectorioCommand;
use App\Application\DirectorioInstitucional\DTOs\DirectorioInstitucionalDTO;
use App\Domain\DirectorioInstitucional\Contracts\DirectorioInstitucionalRepositoryInterface;

class CreateDirectorioHandler
{
    public function __construct(
        private readonly DirectorioInstitucionalRepositoryInterface $repository
    ) {}

    public function handle(CreateDirectorioCommand $command): DirectorioInstitucionalDTO
    {
        return $this->repository->create([
            'secretaria_id' => $command->secretaria_id,
            'nombre_unidad' => $command->nombre,
            'descripcion' => $command->descripcion,
            'titular' => $command->responsable,
            'cargo_responsable' => $command->cargo_responsable,
            'telefono_principal' => $command->telefono,
            'telefono_secundario' => $command->telefono_interno,
            'email_institucional' => $command->email,
            'foto_url' => $command->foto_url,
            'direccion_fisica' => $command->ubicacion,
            'horario_lunes_viernes' => $command->horario,
            'orden' => $command->orden,
            'activo' => $command->activo,
        ]);
    }
}
