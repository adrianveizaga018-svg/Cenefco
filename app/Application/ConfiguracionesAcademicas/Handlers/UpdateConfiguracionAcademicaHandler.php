<?php

namespace App\Application\ConfiguracionesAcademicas\Handlers;

use App\Application\ConfiguracionesAcademicas\Commands\UpdateConfiguracionAcademicaCommand;
use App\Domain\ConfiguracionesAcademicas\Contracts\ConfiguracionAcademicaRepositoryInterface;

class UpdateConfiguracionAcademicaHandler
{
    public function __construct(
        private readonly ConfiguracionAcademicaRepositoryInterface $repository,
    ) {}

    public function handle(UpdateConfiguracionAcademicaCommand $command): void
    {
        $this->repository->update($command->idConf, array_filter([
            'gestion'     => $command->gestion,
            'id_plan'     => $command->idPlan,
            'descripcion' => $command->descripcion,
        ], fn ($v) => $v !== null));
    }
}
