<?php

namespace App\Application\DirectorioInstitucional\Handlers;

use App\Application\DirectorioInstitucional\Commands\UpdateDirectorioCommand;
use App\Application\DirectorioInstitucional\DTOs\DirectorioInstitucionalDTO;
use App\Domain\DirectorioInstitucional\Contracts\DirectorioInstitucionalRepositoryInterface;

class UpdateDirectorioHandler
{
    public function __construct(
        private readonly DirectorioInstitucionalRepositoryInterface $repository
    ) {}

    public function handle(UpdateDirectorioCommand $command): DirectorioInstitucionalDTO
    {
        return $this->repository->update($command->id, [
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
