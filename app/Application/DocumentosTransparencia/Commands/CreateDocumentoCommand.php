<?php

namespace App\Application\DocumentosTransparencia\Commands;

final readonly class CreateDocumentoCommand
{
    public function __construct(
        public int $tipo_documento_id,
        public ?int $secretaria_id,
        public ?int $publicado_por,
        public string $titulo,
        public ?string $descripcion = null,
        public ?string $archivo_url = null,
        public int $gestion = 2024,
        public ?string $fecha_publicacion = null,
        public bool $activo = true,
    ) {}
}
