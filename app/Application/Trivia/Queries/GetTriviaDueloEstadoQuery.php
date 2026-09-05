<?php

namespace App\Application\Trivia\Queries;

final readonly class GetTriviaDueloEstadoQuery
{
    public function __construct(
        public int $partidaId,
        public int $usuarioId,
    ) {}
}
