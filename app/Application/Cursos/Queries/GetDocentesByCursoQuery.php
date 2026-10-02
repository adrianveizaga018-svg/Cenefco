<?php

namespace App\Application\Cursos\Queries;

final readonly class GetDocentesByCursoQuery
{
    public function __construct(
        public int $programa_id,
        public bool $soloVisibles = false,
    ) {}
}
