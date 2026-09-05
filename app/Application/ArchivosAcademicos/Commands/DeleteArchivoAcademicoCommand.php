<?php

namespace App\Application\ArchivosAcademicos\Commands;

final readonly class DeleteArchivoAcademicoCommand
{
    public function __construct(public int $idArch) {}
}
