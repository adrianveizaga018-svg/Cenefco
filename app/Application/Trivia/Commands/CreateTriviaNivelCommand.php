<?php

namespace App\Application\Trivia\Commands;

final readonly class CreateTriviaNivelCommand
{
    public function __construct(
        public int $categoria_id,
        public string $nombre,
        public int $orden = 0,
        public int $puntaje_base = 100,
        public bool $activo = true,
    ) {}
}
