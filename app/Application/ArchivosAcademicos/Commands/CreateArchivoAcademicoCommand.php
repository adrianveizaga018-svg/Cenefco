<?php

namespace App\Application\ArchivosAcademicos\Commands;

final readonly class CreateArchivoAcademicoCommand
{
    public function __construct(
        public int $idArch,
        public ?string $nombre,
        public ?string $ruta,
        public ?string $tipo,
        public int $idUsReg,
    ) {}
}
