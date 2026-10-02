<?php

namespace App\Application\BloquesPlantilla\Handlers;

use App\Application\BloquesPlantilla\Commands\UpdateBloquePlantillaCommand;
use App\Domain\BloquesPlantilla\Contracts\BloquePlantillaRepositoryInterface;

class UpdateBloquePlantillaHandler
{
    public function __construct(
        private readonly BloquePlantillaRepositoryInterface $repository,
    ) {}

    public function handle(UpdateBloquePlantillaCommand $command): void
    {
        $this->repository->update($command->idBloqueplantilla, array_filter([
            'nombre'      => $command->nombre,
            'descripcion' => $command->descripcion,
        ], fn ($v) => $v !== null));
    }
}
