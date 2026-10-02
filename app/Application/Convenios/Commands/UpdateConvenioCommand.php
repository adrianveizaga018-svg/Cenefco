<?php

namespace App\Application\Convenios\Commands;

final readonly class UpdateConvenioCommand
{
    public function __construct(
        public int $id,
        public ?string $nombre = null,
        public ?string $institucion = null,
        public ?string $tipo = null,
        public ?string $descripcion = null,
        public ?string $responsable = null,
        public ?string $contacto_email = null,
        public ?string $contacto_telefono = null,
        public ?string $fecha_inicio = null,
        public ?string $fecha_fin = null,
        public ?string $documento_url = null,
        public ?string $logo_url = null,
        public ?string $estado = null,
        public ?int $orden = null,
    ) {}
}
