<?php

namespace App\Application\Trivia\Handlers;

use App\Application\Trivia\Commands\IniciarTriviaPartidaCommand;
use App\Application\Trivia\DTOs\TriviaPartidaEstadoDTO;
use App\Application\Trivia\DTOs\TriviaPreguntaJuegoDTO;
use App\Application\Trivia\Services\TriviaMotorService;
use App\Domain\Trivia\Contracts\TriviaPartidaRepositoryInterface;
use App\Domain\Trivia\Exceptions\TriviaSinPreguntasDisponiblesException;
use Illuminate\Support\Facades\DB;

class IniciarTriviaPartidaHandler
{
    public function __construct(
        private readonly TriviaPartidaRepositoryInterface $partidaRepository,
        private readonly TriviaMotorService $motor,
    ) {}

    public function handle(IniciarTriviaPartidaCommand $command): array
    {
        return DB::transaction(function () use ($command) {
            $pregunta = $this->motor->seleccionarSiguientePregunta($command->categoriaId, []);
            if (! $pregunta) {
                throw new TriviaSinPreguntasDisponiblesException();
            }

            $jugador = $this->partidaRepository->crearPartida($command->categoriaId, $command->usuarioId);
            $this->partidaRepository->actualizarProgreso($jugador->id, ['pregunta_actual_id' => $pregunta->id]);

            $jugadorActualizado = $this->partidaRepository->findJugador($jugador->partida_id, $command->usuarioId);

            return [
                'partida' => TriviaPartidaEstadoDTO::fromModel($jugadorActualizado),
                'pregunta' => TriviaPreguntaJuegoDTO::fromDTO($pregunta),
            ];
        });
    }
}
