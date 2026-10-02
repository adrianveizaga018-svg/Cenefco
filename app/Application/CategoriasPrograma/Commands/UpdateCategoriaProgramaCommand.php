<?php

namespace App\Application\CategoriasPrograma\Commands;

final readonly class UpdateCategoriaProgramaCommand
{
    public function __construct(
        public int $id,
        public ?string $nombre = null,
        public ?string $slug = null,
        public ?string $descripcion = null,
        public ?string $imagen_url = null,
        public ?string $imagen_alt = null,
        public ?string $icono = null,
        public ?string $color = null,
        public ?int $orden = null,
        public ?bool $activo = null,
        public ?string $meta_titulo = null,
        public ?string $meta_descripcion = null,
        public ?int $tipo_programa_id = null,
        public ?float $comision_monto = null,
    ) {}
}
