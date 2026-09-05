<?php

namespace App\Application\ConfiguracionesAcademicas\Handlers;

use App\Application\ConfiguracionesAcademicas\Commands\DeleteConfiguracionAcademicaCommand;
use App\Domain\ConfiguracionesAcademicas\Contracts\ConfiguracionAcademicaRepositoryInterface;

class DeleteConfiguracionAcademicaHandler
{
    public function __construct(
        private readonly ConfiguracionAcademicaRepositoryInterface $repository,
    ) {}

    public function handle(DeleteConfiguracionAcademicaCommand $command): void
    {
        $this->repository->delete($command->idConf);
    }
}
