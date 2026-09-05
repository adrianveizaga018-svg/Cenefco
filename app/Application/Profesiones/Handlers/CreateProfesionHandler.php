<?php

namespace App\Application\Profesiones\Handlers;

use App\Application\Profesiones\Commands\CreateProfesionCommand;
use App\Application\Profesiones\DTOs\ProfesionDTO;
use App\Domain\Profesiones\Contracts\ProfesionRepositoryInterface;

class CreateProfesionHandler
{
    public function __construct(private readonly ProfesionRepositoryInterface $repository) {}

    public function handle(CreateProfesionCommand $command): ProfesionDTO
    {
        $row = $this->repository->create([
            'nombre'     => $command->nombre,
            'orden'      => $command->orden,
            'activo'     => $command->activo,
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ]);

        return ProfesionDTO::fromRow($row);
    }
}
