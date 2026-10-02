<?php

namespace App\Application\ProgramasAcademicos\Handlers;

use App\Application\ProgramasAcademicos\Commands\DeleteProgramaAcademicoCommand;
use App\Domain\ProgramasAcademicos\Contracts\ProgramaAcademicoRepositoryInterface;
use App\Domain\ProgramasAcademicos\Exceptions\ProgramaAcademicoConInscritosException;

class DeleteProgramaAcademicoHandler
{
    public function __construct(
        private readonly ProgramaAcademicoRepositoryInterface $repository
    ) {}

    public function handle(DeleteProgramaAcademicoCommand $command): void
    {
        if ($this->repository->tieneInscritos($command->id)) {
            throw new ProgramaAcademicoConInscritosException();
        }

        $this->repository->delete($command->id);
    }
}
