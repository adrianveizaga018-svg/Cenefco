<?php

namespace App\Application\Cursos\Commands;

final readonly class DetachDocenteCommand
{
    public function __construct(
        public int $programa_id,
        public int $docente_id,
    ) {}
}
