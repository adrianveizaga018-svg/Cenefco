<?php

namespace App\Application\CategoriasPrograma\Commands;

final readonly class CreateCategoriaProgramaCommand
{
    public function __construct(
        public string $nombre,
        public ?string $slug = null,
        public ?string $descripcion = null,
        public ?string $imagen_url = null,
        public ?string $imagen_alt = null,
        public ?string $icono = null,
        public ?string $color = null,
        public int $orden = 0,
        public bool $activo = true,
        public ?string $meta_titulo = null,
        public ?string $meta_descripcion = null,
        public ?int $tipo_programa_id = null,
        public float $comision_monto = 0,
    ) {}
}
