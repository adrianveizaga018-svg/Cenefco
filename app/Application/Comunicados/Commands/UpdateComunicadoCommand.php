<?php

namespace App\Application\Comunicados\Commands;

final readonly class UpdateComunicadoCommand
{
    public function __construct(
        public int $id,
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
