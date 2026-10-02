<?php

namespace App\Application\Trivia\QueryHandlers;

use App\Application\Trivia\DTOs\TriviaDueloEstadoDTO;
use App\Application\Trivia\Queries\GetTriviaDueloEstadoQuery;
use App\Application\Trivia\Services\TriviaDueloEstadoService;

class GetTriviaDueloEstadoQueryHandler
{
    public function __construct(
        private readonly TriviaDueloEstadoService $estadoService
    ) {}

    public function handle(GetTriviaDueloEstadoQuery $query): TriviaDueloEstadoDTO
    {
        return $this->estadoService->construir($query->partidaId, $query->usuarioId);
    }
}
