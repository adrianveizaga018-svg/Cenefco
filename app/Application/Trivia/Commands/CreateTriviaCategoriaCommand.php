<?php

namespace App\Application\Trivia\Commands;

final readonly class CreateTriviaCategoriaCommand
{
    public function __construct(
        public string $nombre,
        public ?string $descripcion = null,
        public ?string $imagen_url = null,
        public ?string $color = null,
        public ?int $curso_id = null,
        public int $orden = 0,
        public bool $activo = true,
    ) {}
}
