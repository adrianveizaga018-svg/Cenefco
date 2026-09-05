<?php

namespace App\Application\Trivia\Services;

use App\Application\Trivia\DTOs\TriviaSaldoDTO;
use App\Domain\Trivia\Contracts\TriviaCanjeRepositoryInterface;
use App\Domain\Trivia\Contracts\TriviaPartidaRepositoryInterface;

class TriviaSaldoService
{
    public function __construct(
        private readonly TriviaPartidaRepositoryInterface $partidaRepository,
        private readonly TriviaCanjeRepositoryInterface $canjeRepository,
    ) {}

    public function calcular(int $usuarioId): TriviaSaldoDTO
    {
        $ganado = $this->partidaRepository->puntajeTotalUsuario($usuarioId);
        $gastado = $this->canjeRepository->puntosGastadosUsuario($usuarioId);

        return new TriviaSaldoDTO(
            puntaje_total: $ganado,
            puntos_gastados: $gastado,
            saldo_disponible: max(0, $ganado - $gastado),
        );
    }
}
