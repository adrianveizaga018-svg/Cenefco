<?php

namespace App\Application\Universidades\Handlers;

use App\Application\Universidades\Commands\UpdateUniversidadCommand;
use App\Application\Universidades\DTOs\UniversidadDTO;
use App\Domain\Universidades\Contracts\UniversidadRepositoryInterface;

class UpdateUniversidadHandler
{
    public function __construct(private readonly UniversidadRepositoryInterface $repository) {}

    public function handle(UpdateUniversidadCommand $command): UniversidadDTO
    {
        $this->repository->findById($command->id);

        $changes = array_filter([
            'nombre_universidad' => $command->nombre_universidad,
            'id_ciudad'          => $command->id_ciudad,
            'id_tipouniversidad' => $command->id_tipouniversidad,
            'estado'             => $command->estado,
        ], fn ($v) => $v !== null);

        $this->repository->update($command->id, $changes);

        return UniversidadDTO::fromRow($this->repository->findById($command->id));
    }
}
