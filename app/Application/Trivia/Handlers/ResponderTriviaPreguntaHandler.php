<?php

namespace App\Application\Trivia\Handlers;

use App\Application\Trivia\Commands\ResponderTriviaPreguntaCommand;
use App\Application\Trivia\DTOs\TriviaPartidaEstadoDTO;
use App\Application\Trivia\DTOs\TriviaPreguntaJuegoDTO;
use App\Application\Trivia\Services\TriviaMotorService;
use App\Domain\Trivia\Contracts\TriviaNivelRepositoryInterface;
use App\Domain\Trivia\Contracts\TriviaPartidaRepositoryInterface;
use App\Domain\Trivia\Contracts\TriviaPreguntaRepositoryInterface;
use App\Domain\Trivia\Exceptions\TriviaPartidaFinalizadaException;
use App\Domain\Trivia\Exceptions\TriviaPartidaNotFoundException;
use App\Domain\Trivia\Exceptions\TriviaPreguntaInvalidaException;
use Illuminate\Support\Facades\DB;

class ResponderTriviaPreguntaHandler
{
    public function __construct(
        private readonly TriviaPartidaRepositoryInterface $partidaRepository,
        private readonly TriviaPreguntaRepositoryInterface $preguntaRepository,
        private readonly TriviaNivelRepositoryInterface $nivelRepository,
        private readonly TriviaMotorService $motor,
    ) {}

    public function handle(ResponderTriviaPreguntaCommand $command): array
    {
        return DB::transaction(function () use ($command) {
            $jugador = $this->partidaRepository->findJugador($command->partidaId, $command->usuarioId);
            if (! $jugador) {
                throw new TriviaPartidaNotFoundException($command->partidaId);
            }
            if ($jugador->partida->estado !== 'en_curso') {
                throw new TriviaPartidaFinalizadaException();
            }
            if ((int) $jugador->pregunta_actual_id !== $command->preguntaId) {
                throw new TriviaPreguntaInvalidaException();
            }

            $pregunta = $this->preguntaRepository->findById($command->preguntaId);
            $esCorrecta = $this->motor->evaluarRespuesta($pregunta, $command->opcionId);

            $this->partidaRepository->registrarRespuesta([
                'partida_id' => $command->partidaId,
                'jugador_id' => $jugador->id,
                'pregunta_id' => $command->preguntaId,
                'opcion_id' => $command->opcionId,
                'es_correcta' => $esCorrecta,
                'tiempo_respuesta_ms' => $command->tiempoRespuestaMs,
            ]);

            $puntaje = (int) $jugador->puntaje;
            $vidas = (int) $jugador->vidas;

            if ($esCorrecta) {
                $puntaje += $this->nivelRepository->findById($pregunta->nivel_id)->puntaje_base;
            } else {
                $vidas -= 1;
            }

            $preguntaSiguiente = null;
            $estadoJugadorFinal = 'jugando';
            $partidaFinalizada = false;

            if ($vidas <= 0) {
                $partidaFinalizada = true;
                $estadoJugadorFinal = 'perdedor';
            } else {
                $excluir = $this->partidaRepository->preguntasRespondidasIds($command->partidaId);
                $preguntaSiguiente = $this->motor->seleccionarSiguientePregunta($jugador->partida->categoria_id, $excluir);

                if (! $preguntaSiguiente) {
                    $partidaFinalizada = true;
                    $estadoJugadorFinal = 'ganador';
                }
            }

            $this->partidaRepository->actualizarProgreso($jugador->id, [
                'puntaje' => $puntaje,
                'vidas' => max(0, $vidas),
                'estado' => $estadoJugadorFinal,
                'pregunta_actual_id' => $preguntaSiguiente?->id,
            ]);

            if ($partidaFinalizada) {
                $this->partidaRepository->actualizarEstadoPartida($command->partidaId, 'finalizada');
            }

            $jugadorActualizado = $this->partidaRepository->findJugador($command->partidaId, $command->usuarioId);

            return [
                'es_correcta' => $esCorrecta,
                'partida' => TriviaPartidaEstadoDTO::fromModel($jugadorActualizado),
                'pregunta' => $preguntaSiguiente ? TriviaPreguntaJuegoDTO::fromDTO($preguntaSiguiente) : null,
            ];
        });
    }
}
