<?php

namespace App\Application\InscripcionesDiplomado\Handlers;

use App\Application\InscripcionesDiplomado\Commands\DeleteInscripcionDiplomadoCommand;
use App\Domain\InscripcionesDiplomado\Contracts\InscripcionDiplomadoRepositoryInterface;

class DeleteInscripcionDiplomadoHandler
{
    public function __construct(
        private readonly InscripcionDiplomadoRepositoryInterface $repository,
    ) {}

    public function handle(DeleteInscripcionDiplomadoCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
