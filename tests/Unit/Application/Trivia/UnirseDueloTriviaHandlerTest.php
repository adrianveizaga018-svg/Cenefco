<?php

namespace Tests\Unit\Application\Trivia;

use App\Application\Trivia\Commands\UnirseDueloTriviaCommand;
use App\Application\Trivia\DTOs\TriviaDueloEstadoDTO;
use App\Application\Trivia\Handlers\UnirseDueloTriviaHandler;
use App\Application\Trivia\Services\TriviaDueloEstadoService;
use App\Domain\Trivia\Contracts\TriviaPartidaRepositoryInterface;
use App\Domain\Trivia\Contracts\TriviaPreguntaRepositoryInterface;
use App\Domain\Trivia\Exceptions\TriviaDueloNotFoundException;
use App\Domain\Trivia\Exceptions\TriviaDueloPropioException;
use App\Domain\Trivia\Exceptions\TriviaDueloYaIniciadoException;
use App\Domain\Trivia\Exceptions\TriviaSinPreguntasDisponiblesException;
use Illuminate\Support\Collection;
use Tests\TestCase;

class UnirseDueloTriviaHandlerTest extends TestCase
{
    private function partida(string $estado, int $anfitrionUsuarioId): object
    {
        $partida = new \stdClass();
        $partida->id = 10;
        $partida->estado = $estado;
        $partida->categoria_id = 5;

        $anfitrion = new \stdClass();
        $anfitrion->id = 1;
        $anfitrion->usuario_id = $anfitrionUsuarioId;

        $partida->jugadores = new Collection([$anfitrion]);

        return $partida;
    }

    private function dummyEstado(): TriviaDueloEstadoDTO
    {
        return new TriviaDueloEstadoDTO(
            partida_id: 10, codigo_sala: 'ABC123', estado_partida: 'en_curso', categoria_id: 5,
            total_preguntas: 5, mi_puntaje: 0, mi_pregunta_indice: 0, mi_estado: 'jugando',
            rival: null, pregunta_actual: null, resultado: null,
        );
    }

    public function test_lanza_excepcion_si_la_sala_no_existe(): void
    {
        $partidaRepository = \Mockery::mock(TriviaPartidaRepositoryInterface::class);
        $partidaRepository->shouldReceive('findPartidaPorCodigoConLock')->once()->with('ABC123')->andReturnNull();

        $preguntaRepository = \Mockery::mock(TriviaPreguntaRepositoryInterface::class);
        $preguntaRepository->shouldNotReceive('seleccionarParaDuelo');

        $estadoService = \Mockery::mock(TriviaDueloEstadoService::class);
        $handler = new UnirseDueloTriviaHandler($partidaRepository, $preguntaRepository, $estadoService);

        $this->expectException(TriviaDueloNotFoundException::class);
        $handler->handle(new UnirseDueloTriviaCommand(codigoSala: 'ABC123', usuarioId: 2));
    }

    public function test_lanza_excepcion_si_la_sala_ya_esta_en_curso(): void
    {
        $partidaRepository = \Mockery::mock(TriviaPartidaRepositoryInterface::class);
        $partidaRepository->shouldReceive('findPartidaPorCodigoConLock')->once()->andReturn($this->partida('en_curso', 1));

        $preguntaRepository = \Mockery::mock(TriviaPreguntaRepositoryInterface::class);
        $estadoService = \Mockery::mock(TriviaDueloEstadoService::class);
        $handler = new UnirseDueloTriviaHandler($partidaRepository, $preguntaRepository, $estadoService);

        $this->expectException(TriviaDueloYaIniciadoException::class);
        $handler->handle(new UnirseDueloTriviaCommand(codigoSala: 'ABC123', usuarioId: 2));
    }

    public function test_lanza_excepcion_si_intenta_unirse_a_su_propia_sala(): void
    {
        $partidaRepository = \Mockery::mock(TriviaPartidaRepositoryInterface::class);
        $partidaRepository->shouldReceive('findPartidaPorCodigoConLock')->once()->andReturn($this->partida('esperando', 1));

        $preguntaRepository = \Mockery::mock(TriviaPreguntaRepositoryInterface::class);
        $estadoService = \Mockery::mock(TriviaDueloEstadoService::class);
        $handler = new UnirseDueloTriviaHandler($partidaRepository, $preguntaRepository, $estadoService);

        $this->expectException(TriviaDueloPropioException::class);
        $handler->handle(new UnirseDueloTriviaCommand(codigoSala: 'ABC123', usuarioId: 1));
    }

    public function test_lanza_excepcion_si_no_hay_suficientes_preguntas(): void
    {
        $partidaRepository = \Mockery::mock(TriviaPartidaRepositoryInterface::class);
        $partidaRepository->shouldReceive('findPartidaPorCodigoConLock')->once()->andReturn($this->partida('esperando', 1));

        $preguntaRepository = \Mockery::mock(TriviaPreguntaRepositoryInterface::class);
        $preguntaRepository->shouldReceive('seleccionarParaDuelo')->once()->with(5, 5)->andReturn([100]);

        $partidaRepository->shouldNotReceive('agregarSegundoJugador');

        $estadoService = \Mockery::mock(TriviaDueloEstadoService::class);
        $handler = new UnirseDueloTriviaHandler($partidaRepository, $preguntaRepository, $estadoService);

        $this->expectException(TriviaSinPreguntasDisponiblesException::class);
        $handler->handle(new UnirseDueloTriviaCommand(codigoSala: 'ABC123', usuarioId: 2));
    }

    public function test_une_al_segundo_jugador_y_arranca_el_duelo(): void
    {
        $partidaRepository = \Mockery::mock(TriviaPartidaRepositoryInterface::class);
        $partidaRepository->shouldReceive('findPartidaPorCodigoConLock')->once()->andReturn($this->partida('esperando', 1));

        $preguntaRepository = \Mockery::mock(TriviaPreguntaRepositoryInterface::class);
        $preguntaRepository->shouldReceive('seleccionarParaDuelo')->once()->with(5, 5)->andReturn([100, 101, 102, 103, 104]);

        $nuevoJugador = new \stdClass();
        $nuevoJugador->id = 2;
        $partidaRepository->shouldReceive('agregarSegundoJugador')->once()->with(10, 2)->andReturn($nuevoJugador);

        $partidaRepository->shouldReceive('actualizarPreguntasPartida')->once()->with(10, [100, 101, 102, 103, 104]);
        $partidaRepository->shouldReceive('actualizarProgreso')->once()->with(1, [
            'pregunta_actual_id' => 100, 'pregunta_indice' => 0, 'estado' => 'jugando',
        ]);
        $partidaRepository->shouldReceive('actualizarProgreso')->once()->with(2, [
            'pregunta_actual_id' => 100, 'pregunta_indice' => 0, 'estado' => 'jugando',
        ]);
        $partidaRepository->shouldReceive('actualizarEstadoPartida')->once()->with(10, 'en_curso');

        $estadoService = \Mockery::mock(TriviaDueloEstadoService::class);
        $estadoService->shouldReceive('construir')->once()->with(10, 2)->andReturn($this->dummyEstado());

        $handler = new UnirseDueloTriviaHandler($partidaRepository, $preguntaRepository, $estadoService);
        $resultado = $handler->handle(new UnirseDueloTriviaCommand(codigoSala: 'ABC123', usuarioId: 2));

        $this->assertSame('en_curso', $resultado->estado_partida);
    }
}
