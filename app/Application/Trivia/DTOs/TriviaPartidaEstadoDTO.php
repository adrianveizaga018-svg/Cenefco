<?php

namespace App\Application\Trivia\DTOs;

final readonly class TriviaPartidaEstadoDTO
{
    public function __construct(
        public int $partida_id,
        public int $jugador_id,
        public int $categoria_id,
        public string $estado_partida,
        public int $puntaje,
        public int $vidas,
        public string $estado_jugador,
    ) {}

    public static function fromModel(object $jugador): self
    {
        return new self(
            partida_id: $jugador->partida_id,
            jugador_id: $jugador->id,
            categoria_id: $jugador->partida->categoria_id,
            estado_partida: $jugador->partida->estado,
            puntaje: (int) $jugador->puntaje,
            vidas: (int) $jugador->vidas,
            estado_jugador: $jugador->estado,
        );
    }
}
