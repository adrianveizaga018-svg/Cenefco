<?php

namespace App\Application\Comunicados\Commands;

final readonly class CreateComunicadoCommand
{
    public function __construct(
        public string $titulo,
        public ?string $resumen,
        public ?string $cuerpo,
        public ?string $imagen_url,
        public ?string $archivo_url,
        public string $estado,
        public bool $destacado,
        public ?string $fecha_publicacion,
    ) {}
}
