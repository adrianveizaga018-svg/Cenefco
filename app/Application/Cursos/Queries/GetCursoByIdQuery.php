<?php

namespace App\Application\Cursos\Queries;

final readonly class GetCursoByIdQuery
{
    public function __construct(
        public int $id,
        public bool $soloPublicados = false,
    ) {}
}
