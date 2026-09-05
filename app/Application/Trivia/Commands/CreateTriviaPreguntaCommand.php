<?php

namespace App\Application\Trivia\Commands;

final readonly class CreateTriviaPreguntaCommand
{
    public function __construct(
        public int $categoria_id,
        public int $nivel_id,
        public string $enunciado,
        public ?string $imagen_url,
        public int $tiempo_limite_segundos,
        public bool $activo,
        public array $opciones,
    ) {}
}
