<?php

namespace App\Application\CategoriasNoticia\Commands;

final readonly class CreateCategoriaNoticiaCommand
{
    public function __construct(
        public string $nombre,
        public ?string $descripcion = null,
        public ?string $color_hex = null,
        public bool $activa = true,
    ) {}
}
