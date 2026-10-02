<?php

namespace App\Application\ArchivosAcademicos\Handlers;

use App\Application\ArchivosAcademicos\Commands\CreateArchivoAcademicoCommand;
use App\Application\ArchivosAcademicos\DTOs\ArchivoAcademicoDTO;
use App\Domain\ArchivosAcademicos\Contracts\ArchivoAcademicoRepositoryInterface;

class CreateArchivoAcademicoHandler
{
    public function __construct(
        private readonly ArchivoAcademicoRepositoryInterface $repository,
    ) {}

    public function handle(CreateArchivoAcademicoCommand $command): ArchivoAcademicoDTO
    {
        $row = $this->repository->create([
            'id_arch'   => $command->idArch,
            'nombre'    => $command->nombre,
            'ruta'      => $command->ruta,
            'tipo'      => $command->tipo,
            'id_us_reg' => $command->idUsReg,
            'fecha_reg' => now(),
            'estado'    => 1,
        ]);

        return ArchivoAcademicoDTO::fromRow($row);
    }
}
