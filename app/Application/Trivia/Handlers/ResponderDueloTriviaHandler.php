<?php

namespace App\Application\Trivia\Handlers;

use App\Application\Trivia\Commands\ResponderDueloTriviaCommand;
use App\Application\Trivia\DTOs\TriviaDueloEstadoDTO;
use App\Application\Trivia\Services\TriviaDueloEstadoService;
use App\Application\Trivia\Services\TriviaMotorService;
use App\Domain\Trivia\Contracts\TriviaNivelRepositoryInterface;
use App\Domain\Trivia\Contracts\TriviaPartidaRepositoryInterface;
use App\Domain\Trivia\Contracts\TriviaPreguntaRepositoryInterface;
use App\Domain\Trivia\Exceptions\TriviaPartidaFinalizadaException;
use App\Domain\Trivia\Exceptions\TriviaPartidaNotFoundException;
use App\Domain\Trivia\Exceptions\TriviaPreguntaInvalidaException;
use Illuminate\Support\Facades\DB;

class ResponderDueloTriviaHandler
{
    private const ESTADOS_TERMINADO = ['terminado', 'ganador', 'perdedor', 'empate'];

    public function __construct(
        private readonly TriviaPartidaRepositoryInterface $partidaRepository,
        private readonly TriviaPreguntaRepositoryInterface $preguntaRepository,
        private readonly TriviaNivelRepositoryInterface $nivelRepository,
        private readonly TriviaMotorService $motor,
        private readonly TriviaDueloEstadoService $estadoService,
    ) {}

    public function handle(ResponderDueloTriviaCommand $command): TriviaDueloEstadoDTO
    {
        return DB::transaction(function () use ($command) {
            $partidaLock = $this->partidaRepository->findPartidaConLock($command->partidaId);
            if (! $partidaLock) {
                throw new TriviaPartidaNotFoundException($command->partidaId);
            }
            if ($partidaLock->estado !== 'en_curso') {
                throw new TriviaPartidaFinalizadaException();
            }

            $jugador = $this->partidaRepository->findJugador($command->partidaId, $command->usuarioId);
            if (! $jugador) {
                throw new TriviaPartidaNotFoundException($command->partidaId);
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
            if ($esCorrecta) {
                $puntaje += $this->nivelRepository->findById($pregunta->nivel_id)->puntaje_base;
            }

            $preguntasIds = is_array($partidaLock->preguntas_ids) ? $partidaLock->preguntas_ids : [];
            $nuevoIndice = (int) $jugador->pregunta_indice + 1;

            if ($nuevoIndice < count($preguntasIds)) {
                $this->partidaRepository->actualizarProgreso($jugador->id, [
                    'puntaje' => $puntaje,
                    'pregunta_indice' => $nuevoIndice,
                    'pregunta_actual_id' => $preguntasIds[$nuevoIndice],
                    'estado' => 'jugando',
                ]);
            } else {
                $this->partidaRepository->actualizarProgreso($jugador->id, [
                    'puntaje' => $puntaje,
                    'pregunta_indice' => $nuevoIndice,
                    'pregunta_actual_id' => null,
                    'estado' => 'terminado',
                ]);

                $rival = $this->partidaRepository->otroJugador($command->partidaId, $command->usuarioId);
                if ($rival && in_array($rival->estado, self::ESTADOS_TERMINADO, true)) {
                    $this->resolverGanador($command->partidaId, $jugador->id, $puntaje, $rival);
                }
            }

            return $this->estadoService->construir($command->partidaId, $command->usuarioId);
        });
    }

    private function resolverGanador(int $partidaId, int $miJugadorId, int $miPuntaje, object $rival): void
    {
        $rivalPuntaje = (int) $rival->puntaje;

        if ($miPuntaje > $rivalPuntaje) {
            $this->partidaRepository->actualizarProgreso($miJugadorId, ['estado' => 'ganador']);
            $this->partidaRepository->actualizarProgreso($rival->id, ['estado' => 'perdedor']);
        } elseif ($miPuntaje < $rivalPuntaje) {
            $this->partidaRepository->actualizarProgreso($miJugadorId, ['estado' => 'perdedor']);
            $this->partidaRepository->actualizarProgreso($rival->id, ['estado' => 'ganador']);
        } else {
            $this->partidaRepository->actualizarProgreso($miJugadorId, ['estado' => 'empate']);
            $this->partidaRepository->actualizarProgreso($rival->id, ['estado' => 'empate']);
        }

        $this->partidaRepository->actualizarEstadoPartida($partidaId, 'finalizada');
    }
}
