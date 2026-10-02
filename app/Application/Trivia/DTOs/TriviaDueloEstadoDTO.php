<?php

namespace App\Application\Trivia\DTOs;

final readonly class TriviaDueloEstadoDTO
{
    public function __construct(
        public int $partida_id,
        public string $codigo_sala,
        public string $estado_partida,
        public int $categoria_id,
        public int $total_preguntas,
        public int $mi_puntaje,
        public int $mi_pregunta_indice,
        public string $mi_estado,
        public ?TriviaDueloRivalDTO $rival,
        public ?TriviaPreguntaJuegoDTO $pregunta_actual,
        public ?string $resultado,
    ) {}
}
