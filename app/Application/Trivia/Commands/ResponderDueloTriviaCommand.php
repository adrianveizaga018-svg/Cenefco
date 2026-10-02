<?php

namespace App\Application\Trivia\Commands;

final readonly class ResponderDueloTriviaCommand
{
    public function __construct(
        public int $partidaId,
        public int $usuarioId,
        public int $preguntaId,
        public ?int $opcionId,
        public int $tiempoRespuestaMs,
    ) {}
}
