<?php

namespace App\Application\Trivia\Services;

use App\Application\Trivia\DTOs\TriviaDueloEstadoDTO;
use App\Application\Trivia\DTOs\TriviaDueloRivalDTO;
use App\Application\Trivia\DTOs\TriviaPreguntaJuegoDTO;
use App\Domain\Trivia\Contracts\TriviaPartidaRepositoryInterface;
use App\Domain\Trivia\Contracts\TriviaPreguntaRepositoryInterface;
use App\Domain\Trivia\Exceptions\TriviaPartidaNotFoundException;

class TriviaDueloEstadoService
{
    public function __construct(
        private readonly TriviaPartidaRepositoryInterface $partidaRepository,
        private readonly TriviaPreguntaRepositoryInterface $preguntaRepository,
    ) {}

    public function construir(int $partidaId, int $usuarioId): TriviaDueloEstadoDTO
    {
        $jugador = $this->partidaRepository->findJugador($partidaId, $usuarioId);
        if (! $jugador) {
            throw new TriviaPartidaNotFoundException($partidaId);
        }

        $rivalModel = $this->partidaRepository->otroJugador($partidaId, $usuarioId);

        $preguntaActual = null;
        if ($jugador->pregunta_actual_id) {
            $preguntaDTO = $this->preguntaRepository->findById((int) $jugador->pregunta_actual_id);
            $preguntaActual = TriviaPreguntaJuegoDTO::fromDTO($preguntaDTO);
        }

        $preguntasIds = $jugador->partida->preguntas_ids ?? [];
        $totalPreguntas = is_array($preguntasIds) ? count($preguntasIds) : 0;

        $resultado = null;
        if ($jugador->partida->estado === 'finalizada') {
            $resultado = match ($jugador->estado) {
                'ganador' => 'ganaste',
                'perdedor' => 'perdiste',
                'empate' => 'empate',
                default => null,
            };
        }

        return new TriviaDueloEstadoDTO(
            partida_id: $partidaId,
            codigo_sala: $jugador->partida->codigo_sala,
            estado_partida: $jugador->partida->estado,
            categoria_id: $jugador->partida->categoria_id,
            total_preguntas: $totalPreguntas,
            mi_puntaje: (int) $jugador->puntaje,
            mi_pregunta_indice: (int) $jugador->pregunta_indice,
            mi_estado: $jugador->estado,
            rival: TriviaDueloRivalDTO::fromModel($rivalModel),
            pregunta_actual: $preguntaActual,
            resultado: $resultado,
        );
    }
}
