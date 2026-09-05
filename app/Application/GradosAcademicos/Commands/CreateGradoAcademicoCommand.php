<?php

namespace App\Application\GradosAcademicos\Commands;

final readonly class CreateGradoAcademicoCommand
{
    public function __construct(
        public string $nombre,
        public string $abreviatura,
        public bool   $requiere_titulo,
        public int    $orden,
        public bool   $activo,
    ) {}
}
