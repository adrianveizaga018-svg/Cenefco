<?php

namespace App\Application\SpeechVentas\Commands;

final readonly class CreateSpeechVentasCommand
{
    public function __construct(
        public string $titulo,
        public ?string $categoria,
        public string $contenido,
        public ?string $palabrasClave,
        public bool $activo,
        public int $orden,
    ) {}
}
