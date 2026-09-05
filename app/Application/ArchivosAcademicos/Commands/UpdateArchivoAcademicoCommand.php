<?php

namespace App\Application\ArchivosAcademicos\Commands;

final readonly class UpdateArchivoAcademicoCommand
{
    public function __construct(
        public int $idArch,
        public ?string $nombre,
        public ?string $ruta,
        public ?string $tipo,
    ) {}
}
