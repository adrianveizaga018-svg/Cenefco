<?php

namespace App\Application\InscripcionesDiplomado\Handlers;

use App\Application\InscripcionesDiplomado\Commands\ConvertirInscripcionDiplomadoCommand;
use App\Domain\InscripcionesDiplomado\Contracts\InscripcionDiplomadoRepositoryInterface;

class ConvertirInscripcionDiplomadoHandler
{
    public function __construct(
        private readonly InscripcionDiplomadoRepositoryInterface $repository,
    ) {}

    public function handle(ConvertirInscripcionDiplomadoCommand $command): array
    {
        return $this->repository->convertirInscripcion(
            $command->id,
            $command->idImp,
            $command->idPlan,
        );
    }
}
