<?php

namespace App\Application\Trivia\DTOs;

final readonly class TriviaRankingItemDTO
{
    public function __construct(
        public int $posicion,
        public int $usuario_id,
        public string $nombre,
        public ?string $avatar_url,
        public int $puntaje_total,
        public int $partidas_jugadas,
        public int $partidas_ganadas,
    ) {}

    public static function fromRow(object $row, int $posicion): self
    {
        return new self(
            posicion: $posicion,
            usuario_id: (int) $row->usuario_id,
            nombre: trim($row->nombre.' '.($row->apellido ?? '')),
            avatar_url: $row->avatar_url,
            puntaje_total: (int) $row->puntaje_total,
            partidas_jugadas: (int) $row->partidas_jugadas,
            partidas_ganadas: (int) $row->partidas_ganadas,
        );
    }
}
