<?php

namespace App\Application\EfectosEspeciales\Commands;

final readonly class CreateEfectoEspecialCommand
{
    public function __construct(
        public string  $nombre,
        public string  $tipo_efecto,
        public string  $fecha_inicio,
        public string  $fecha_fin,
        public ?string $color_primario   = '#ffffff',
        public ?string $color_secundario = null,
        public int     $intensidad       = 50,
        public bool    $activo           = true,
    ) {}
}
