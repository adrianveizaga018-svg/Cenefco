<?php

namespace App\Application\Trivia\Handlers;

use App\Application\Trivia\Commands\UnirseDueloTriviaCommand;
use App\Application\Trivia\DTOs\TriviaDueloEstadoDTO;
use App\Application\Trivia\Services\TriviaDueloEstadoService;
use App\Domain\Trivia\Contracts\TriviaPartidaRepositoryInterface;
use App\Domain\Trivia\Contracts\TriviaPreguntaRepositoryInterface;
use App\Domain\Trivia\Exceptions\TriviaDueloNotFoundException;
use App\Domain\Trivia\Exceptions\TriviaDueloPropioException;
use App\Domain\Trivia\Exceptions\TriviaDueloYaIniciadoException;
use App\Domain\Trivia\Exceptions\TriviaSinPreguntasDisponiblesException;
use Illuminate\Support\Facades\DB;

class UnirseDueloTriviaHandler
{
    private const CANTIDAD_PREGUNTAS = 5;

    public function __construct(
        private readonly TriviaPartidaRepositoryInterface $partidaRepository,
        private readonly TriviaPreguntaRepositoryInterface $preguntaRepository,
        private readonly TriviaDueloEstadoService $estadoService,
    ) {}

    public function handle(UnirseDueloTriviaCommand $command): TriviaDueloEstadoDTO
    {
        return DB::transaction(function () use ($command) {
            $partida = $this->partidaRepository->findPartidaPorCodigoConLock($command->codigoSala);
            if (! $partida) {
                throw new TriviaDueloNotFoundException($command->codigoSala);
            }
            if ($partida->estado !== 'esperando') {
                throw new TriviaDueloYaIniciadoException();
            }

            $anfitrion = $partida->jugadores->first();
            if ($anfitrion && (int) $anfitrion->usuario_id === $command->usuarioId) {
                throw new TriviaDueloPropioException();
            }

            $preguntasIds = $this->preguntaRepository->seleccionarParaDuelo($partida->categoria_id, self::CANTIDAD_PREGUNTAS);
            if (count($preguntasIds) < 2) {
                throw new TriviaSinPreguntasDisponiblesException();
            }

            $nuevoJugador = $this->partidaRepository->agregarSegundoJugador($partida->id, $command->usuarioId);

            $this->partidaRepository->actualizarPreguntasPartida($partida->id, $preguntasIds);
            $this->partidaRepository->actualizarProgreso($anfitrion->id, [
                'pregunta_actual_id' => $preguntasIds[0],
                'pregunta_indice' => 0,
                'estado' => 'jugando',
            ]);
            $this->partidaRepository->actualizarProgreso($nuevoJugador->id, [
                'pregunta_actual_id' => $preguntasIds[0],
                'pregunta_indice' => 0,
                'estado' => 'jugando',
            ]);
            $this->partidaRepository->actualizarEstadoPartida($partida->id, 'en_curso');

            return $this->estadoService->construir($partida->id, $command->usuarioId);
        });
    }
}
