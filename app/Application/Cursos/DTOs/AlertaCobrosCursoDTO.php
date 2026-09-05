<?php

namespace App\Application\Cursos\DTOs;

final readonly class AlertaCobrosCursoDTO
{
    public function __construct(
        public int $id_imp,
        public int $proximas,
        public int $vencidas,
    ) {}
}
