<?php

namespace App\Application\HistoriaMunicipio\Commands;

final readonly class CreateHistoriaMunicipioCommand
{
    public function __construct(
        public string $titulo,
        public ?string $contenido = null,
        public ?string $fecha_inicio = null,
        public ?string $fecha_fin = null,
        public ?string $imagen_url = null,
        public int $orden = 0,
        public bool $activo = true,
    ) {}
}
