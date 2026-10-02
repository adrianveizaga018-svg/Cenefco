<?php

namespace App\Application\GradosAcademicos\Commands;

final readonly class UpdateGradoAcademicoCommand
{
    public function __construct(
        public int     $id,
        public ?string $nombre,
        public ?string $abreviatura,
        public ?bool   $requiere_titulo,
        public ?int    $orden,
        public ?bool   $activo,
    ) {}
}
