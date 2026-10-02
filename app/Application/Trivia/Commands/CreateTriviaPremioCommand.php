<?php

namespace App\Application\Trivia\Commands;

final readonly class CreateTriviaPremioCommand
{
    public function __construct(
        public string $nombre,
        public ?string $descripcion,
        public string $tipo,
        public ?string $imagen_url,
        public int $costo_puntos,
        public ?int $stock,
        public bool $activo,
        public int $orden,
    ) {}
}
