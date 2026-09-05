<?php

namespace App\Application\Comunicados\Handlers;

use App\Application\Comunicados\Commands\DeleteComunicadoCommand;
use App\Domain\Comunicados\Contracts\ComunicadoRepositoryInterface;
use App\Domain\Comunicados\Exceptions\ComunicadoNotFoundException;

class DeleteComunicadoHandler
{
    public function __construct(
        private readonly ComunicadoRepositoryInterface $repository
    ) {}

    public function handle(DeleteComunicadoCommand $command): void
    {
        $deleted = $this->repository->delete($command->id);
        if (! $deleted) {
            throw new ComunicadoNotFoundException($command->id);
        }
    }
}
