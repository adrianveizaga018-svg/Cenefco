<?php

namespace App\Application\Autoridades\Handlers;

use App\Application\Autoridades\Commands\CreateAutoridadCommand;
use App\Application\Autoridades\DTOs\AutoridadDTO;
use App\Domain\Autoridades\Contracts\AutoridadRepositoryInterface;

class CreateAutoridadHandler
{
    public function __construct(private readonly AutoridadRepositoryInterface $repository) {}

    public function handle(CreateAutoridadCommand $command): AutoridadDTO
    {
        return $this->repository->create([
            'secretaria_id' => $command->secretaria_id,
            'nombre' => $command->nombre,
            'apellido' => $command->apellido,
            'cargo' => $command->cargo,
            'tipo' => $command->tipo,
            'perfil_profesional' => $command->perfil_profesional,
            'email_institucional' => $command->email_institucional,
            'foto_url' => $command->foto_url,
            'orden' => $command->orden,
            'activo' => $command->activo,
            'publicado_web' => $command->publicado_web,
            'fecha_inicio_cargo' => $command->fecha_inicio_cargo,
            'fecha_fin_cargo' => $command->fecha_fin_cargo,
        ]);
    }
}
