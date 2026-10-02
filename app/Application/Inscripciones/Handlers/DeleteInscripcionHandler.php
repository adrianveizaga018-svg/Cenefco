<?php

namespace App\Application\Inscripciones\Handlers;

use App\Application\Inscripciones\Commands\DeleteInscripcionCommand;
use App\Domain\Inscripciones\Contracts\InscripcionRepositoryInterface;

class DeleteInscripcionHandler
{
    public function __construct(
        private readonly InscripcionRepositoryInterface $repository
    ) {}

    public function handle(DeleteInscripcionCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
