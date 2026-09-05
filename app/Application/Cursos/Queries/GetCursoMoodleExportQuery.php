<?php

namespace App\Application\Cursos\Queries;

final readonly class GetCursoMoodleExportQuery
{
    public function __construct(
        public int $idImp,
    ) {}
}
