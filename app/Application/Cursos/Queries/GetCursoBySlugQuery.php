<?php

namespace App\Application\Cursos\Queries;

final readonly class GetCursoBySlugQuery
{
    public function __construct(
        public string $slug,
        public bool $soloPublicados = false,
    ) {}
}
