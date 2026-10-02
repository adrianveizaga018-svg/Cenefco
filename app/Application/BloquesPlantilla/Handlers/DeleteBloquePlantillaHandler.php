<?php

namespace App\Application\BloquesPlantilla\Handlers;

use App\Application\BloquesPlantilla\Commands\DeleteBloquePlantillaCommand;
use App\Domain\BloquesPlantilla\Contracts\BloquePlantillaRepositoryInterface;

class DeleteBloquePlantillaHandler
{
    public function __construct(
        private readonly BloquePlantillaRepositoryInterface $repository,
    ) {}

    public function handle(DeleteBloquePlantillaCommand $command): void
    {
        $this->repository->delete($command->idBloqueplantilla);
    }
}
