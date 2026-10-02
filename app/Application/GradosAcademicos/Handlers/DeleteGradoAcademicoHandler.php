<?php

namespace App\Application\GradosAcademicos\Handlers;

use App\Application\GradosAcademicos\Commands\DeleteGradoAcademicoCommand;
use App\Domain\GradosAcademicos\Contracts\GradoAcademicoRepositoryInterface;

class DeleteGradoAcademicoHandler
{
    public function __construct(private readonly GradoAcademicoRepositoryInterface $repository) {}

    public function handle(DeleteGradoAcademicoCommand $command): void
    {
        $this->repository->findById($command->id);
        $this->repository->delete($command->id);
    }
}
