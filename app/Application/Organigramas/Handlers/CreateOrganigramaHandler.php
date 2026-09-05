<?php

namespace App\Application\Organigramas\Handlers;

use App\Application\Organigramas\Commands\CreateOrganigramaCommand;
use App\Application\Organigramas\DTOs\OrganigramaDTO;
use App\Domain\Organigramas\Contracts\OrganigramaRepositoryInterface;

class CreateOrganigramaHandler
{
    public function __construct(private readonly OrganigramaRepositoryInterface $repository) {}

    public function handle(CreateOrganigramaCommand $command): OrganigramaDTO
    {
        return $this->repository->create([
            'secretaria_id' => $command->secretaria_id,
            'parent_id' => $command->parent_id,
            'nivel' => $command->nivel,
            'orden' => $command->orden,
            'imagen_url' => $command->imagen_url,
            'fecha_actualizacion' => $command->fecha_actualizacion,
            'vigente' => $command->vigente,
        ]);
    }
}
