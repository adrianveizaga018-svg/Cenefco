<?php

namespace App\Application\PlanesAcademicos\Handlers;

use App\Application\PlanesAcademicos\Commands\DeletePlanAcademicoCommand;
use App\Domain\PlanesAcademicos\Contracts\PlanAcademicoRepositoryInterface;

class DeletePlanAcademicoHandler
{
    public function __construct(
        private readonly PlanAcademicoRepositoryInterface $repository
    ) {}

    public function handle(DeletePlanAcademicoCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
