<?php

namespace App\Application\RevistasCientificas\Commands;

final readonly class CreateRevistaCientificaCommand
{
    public function __construct(
        public int     $id_revistacientifica,
        public int     $id_us_reg,
        public int     $num_revistacientifica,
        public string  $titulo_revistacientifica,
        public ?string $descripcion_revistacientifica,
        public ?string $fecha_publicacion,
        public ?string $archivo,
        public int     $estado,
    ) {}
}
