<?php

namespace App\Application\Trivia\DTOs;

final readonly class TriviaPreguntaJuegoDTO
{
    public function __construct(
        public int $id,
        public int $categoria_id,
        public int $nivel_id,
        public string $enunciado,
        public ?string $imagen_url,
        public int $tiempo_limite_segundos,
        public array $opciones,
    ) {}

    public static function fromDTO(TriviaPreguntaDTO $pregunta): self
    {
        return new self(
            id: $pregunta->id,
            categoria_id: $pregunta->categoria_id,
            nivel_id: $pregunta->nivel_id,
            enunciado: $pregunta->enunciado,
            imagen_url: $pregunta->imagen_url,
            tiempo_limite_segundos: $pregunta->tiempo_limite_segundos,
            opciones: array_map(
                fn (TriviaOpcionDTO $opcion) => ['id' => $opcion->id, 'texto' => $opcion->texto],
                $pregunta->opciones
            ),
        );
    }
}
