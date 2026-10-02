<?php

namespace App\Application\Trivia\DTOs;

final readonly class TriviaDueloRivalDTO
{
    public function __construct(
        public int $usuario_id,
        public string $nombre,
        public ?string $avatar_url,
        public int $puntaje,
        public int $pregunta_indice,
        public string $estado,
    ) {}

    public static function fromModel(?object $jugador): ?self
    {
        if (! $jugador) {
            return null;
        }

        return new self(
            usuario_id: (int) $jugador->usuario_id,
            nombre: trim(($jugador->usuario->nombre ?? '').' '.($jugador->usuario->apellido ?? '')),
            avatar_url: $jugador->usuario->avatar_url ?? null,
            puntaje: (int) $jugador->puntaje,
            pregunta_indice: (int) $jugador->pregunta_indice,
            estado: $jugador->estado,
        );
    }
}
