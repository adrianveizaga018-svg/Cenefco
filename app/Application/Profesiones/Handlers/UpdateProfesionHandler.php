<?php

namespace App\Application\Profesiones\Handlers;

use App\Application\Profesiones\Commands\UpdateProfesionCommand;
use App\Application\Profesiones\DTOs\ProfesionDTO;
use App\Domain\Profesiones\Contracts\ProfesionRepositoryInterface;

class UpdateProfesionHandler
{
    public function __construct(private readonly ProfesionRepositoryInterface $repository) {}

    public function handle(UpdateProfesionCommand $command): ProfesionDTO
    {
        $this->repository->findById($command->id);

        $changes = array_filter([
            'nombre' => $command->nombre,
            'orden'  => $command->orden,
        ], fn ($v) => $v !== null);

        if ($command->activo !== null) {
            $changes['activo'] = $command->activo;
        }
        $changes['updated_at'] = now()->toDateTimeString();

        $this->repository->update($command->id, $changes);

        return ProfesionDTO::fromRow($this->repository->findById($command->id));
    }
}
