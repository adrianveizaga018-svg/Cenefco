<?php

namespace App\Application\EfectosEspeciales\Handlers;

use App\Application\EfectosEspeciales\Commands\DeleteEfectoEspecialCommand;
use App\Domain\EfectosEspeciales\Contracts\EfectoEspecialRepositoryInterface;

class DeleteEfectoEspecialHandler
{
    public function __construct(private readonly EfectoEspecialRepositoryInterface $repository) {}

    public function handle(DeleteEfectoEspecialCommand $command): bool
    {
        return $this->repository->delete($command->id);
    }
}
