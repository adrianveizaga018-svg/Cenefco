<?php

namespace App\Application\Trivia\Services;

use App\Application\Trivia\DTOs\TriviaPreguntaDTO;
use App\Domain\Trivia\Contracts\TriviaNivelRepositoryInterface;
use App\Domain\Trivia\Contracts\TriviaPreguntaRepositoryInterface;

class TriviaMotorService
{
    public function __construct(
        private readonly TriviaPreguntaRepositoryInterface $preguntaRepository,
        private readonly TriviaNivelRepositoryInterface $nivelRepository,
    ) {}

    public function seleccionarSiguientePregunta(int $categoriaId, array $excluirIds): ?TriviaPreguntaDTO
    {
        return $this->preguntaRepository->siguienteParaJuego($categoriaId, $excluirIds);
    }

    public function evaluarRespuesta(TriviaPreguntaDTO $pregunta, ?int $opcionId): bool
    {
        if ($opcionId === null) {
            return false;
        }

        foreach ($pregunta->opciones as $opcion) {
            if ($opcion->id === $opcionId) {
                return $opcion->es_correcta;
            }
        }

        return false;
    }

    public function puntajePorNivel(int $nivelId): int
    {
        return $this->nivelRepository->findById($nivelId)->puntaje_base;
    }
}
