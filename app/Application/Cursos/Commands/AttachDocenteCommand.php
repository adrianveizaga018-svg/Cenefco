<?php

namespace App\Application\Cursos\Commands;

final readonly class AttachDocenteCommand
{
    public function __construct(
        public int $programa_id,
        public int $docente_id,
        public int $orden = 0,
    ) {}
}
