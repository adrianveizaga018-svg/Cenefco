<?php

namespace App\Application\ArchivosAcademicos\Handlers;

use App\Application\ArchivosAcademicos\Commands\DeleteArchivoAcademicoCommand;
use App\Domain\ArchivosAcademicos\Contracts\ArchivoAcademicoRepositoryInterface;

class DeleteArchivoAcademicoHandler
{
    public function __construct(
        private readonly ArchivoAcademicoRepositoryInterface $repository,
    ) {}

    public function handle(DeleteArchivoAcademicoCommand $command): void
    {
        $this->repository->delete($command->idArch);
    }
}
