<?php

namespace App\Application\EfectosEspeciales\Handlers;

use App\Application\EfectosEspeciales\Commands\UpdateEfectoEspecialCommand;
use App\Application\EfectosEspeciales\DTOs\EfectoEspecialDTO;
use App\Domain\EfectosEspeciales\Contracts\EfectoEspecialRepositoryInterface;

class UpdateEfectoEspecialHandler
{
    public function __construct(private readonly EfectoEspecialRepositoryInterface $repository) {}

    public function handle(UpdateEfectoEspecialCommand $command): EfectoEspecialDTO
    {
        return $this->repository->update($command->id, $command->data);
    }
}
