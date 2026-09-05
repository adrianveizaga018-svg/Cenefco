<?php

namespace App\Application\ArchivosAcademicos\Handlers;

use App\Application\ArchivosAcademicos\Commands\UpdateArchivoAcademicoCommand;
use App\Domain\ArchivosAcademicos\Contracts\ArchivoAcademicoRepositoryInterface;

class UpdateArchivoAcademicoHandler
{
    public function __construct(
        private readonly ArchivoAcademicoRepositoryInterface $repository,
    ) {}

    public function handle(UpdateArchivoAcademicoCommand $command): void
    {
        $this->repository->update($command->idArch, array_filter([
            'nombre' => $command->nombre,
            'ruta'   => $command->ruta,
            'tipo'   => $command->tipo,
        ], fn ($v) => $v !== null));
    }
}
