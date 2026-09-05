<?php

namespace App\Application\RevistasCientificas\Commands;

final readonly class UpdateRevistaCientificaCommand
{
    public function __construct(
        public int     $id,
        public ?string $titulo_revistacientifica,
        public ?string $descripcion_revistacientifica,
        public ?string $fecha_publicacion,
        public ?string $archivo,
        public ?int    $estado,
    ) {}
}
