<?php

namespace App\Application\Organigramas\Commands;

final readonly class CreateOrganigramaCommand
{
    public function __construct(
        public int $secretaria_id,
        public ?int $parent_id = null,
        public int $nivel = 0,
        public int $orden = 0,
        public ?string $imagen_url = null,
        public ?string $fecha_actualizacion = null,
        public bool $vigente = true,
    ) {}
}
