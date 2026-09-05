<?php

namespace App\Application\ArchivosAcademicos\Queries;

final readonly class GetArchivoAcademicoByIdQuery
{
    public function __construct(public int $idArch) {}
}
