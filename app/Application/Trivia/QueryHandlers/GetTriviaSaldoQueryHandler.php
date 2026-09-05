<?php

namespace App\Application\Trivia\QueryHandlers;

use App\Application\Trivia\DTOs\TriviaSaldoDTO;
use App\Application\Trivia\Queries\GetTriviaSaldoQuery;
use App\Application\Trivia\Services\TriviaSaldoService;

class GetTriviaSaldoQueryHandler
{
    public function __construct(
        private readonly TriviaSaldoService $saldoService
    ) {}

    public function handle(GetTriviaSaldoQuery $query): TriviaSaldoDTO
    {
        return $this->saldoService->calcular($query->usuarioId);
    }
}
