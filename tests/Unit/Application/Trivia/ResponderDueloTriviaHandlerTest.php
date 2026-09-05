<?php

namespace Tests\Unit\Application\Trivia;

use App\Application\Trivia\Commands\ResponderDueloTriviaCommand;
use App\Application\Trivia\DTOs\TriviaDueloEstadoDTO;
use App\Application\Trivia\DTOs\TriviaNivelDTO;
use App\Application\Trivia\DTOs\TriviaOpcionDTO;
use App\Application\Trivia\DTOs\TriviaPreguntaDTO;
use App\Application\Trivia\Handlers\ResponderDueloTriviaHandler;
use App\Application\Trivia\Services\TriviaDueloEstadoService;
use App\Application\Trivia\Services\TriviaMotorService;
use App\Domain\Trivia\Contracts\TriviaNivelRepositoryInterface;
use App\Domain\Trivia\Contracts\TriviaPartidaRepositoryInterface;
use App\Domain\Trivia\Contracts\TriviaPreguntaRepositoryInterface;
use App\Domain\Trivia\Exceptions\TriviaPartidaFinalizadaException;
use App\Domain\Trivia\Exceptions\TriviaPreguntaInvalidaException;
use Tests\TestCase;

class ResponderDueloTriviaHandlerTest extends TestCase
{
    private function pregunta(int $id, int $nivelId = 10): TriviaPreguntaDTO
    {
        return new TriviaPreguntaDTO(
            id: $id, categoria_id: 5, nivel_id: $nivelId, enunciado: '¿Pregunta?',
            imagen_url: null, tiempo_limite_segundos: 20, activo: true, created_at: null,
            opciones: [
                new TriviaOpcionDTO(id: 100, texto: 'A', es_correcta: true, orden: 0),
                new TriviaOpcionDTO(id: 101, texto: 'B', es_correcta: false, orden: 1),
            ],
        );
    }

    private function partidaLock(string $estado, array $preguntasIds): object
    {
        $partida = new \stdClass();
        $partida->id = 10;
        $partida->estado = $estado;
        $partida->preguntas_ids = $preguntasIds;

        return $partida;
    }

    private function jugador(int $id, int $preguntaActualId, int $puntaje = 0, int $indice = 0): object
    {
        $jugador = new \stdClass();
        $jugador->id = $id;
        $jugador->pregunta_actual_id = $preguntaActualId;
        $jugador->puntaje = $puntaje;
        $jugador->pregunta_indice = $indice;

        return $jugador;
    }

    private function rival(int $id, string $estado, int $puntaje): object
    {
        $rival = new \stdClass();
        $rival->id = $id;
        $rival->estado = $estado;
        $rival->puntaje = $puntaje;

        return $rival;
    }

    private function dummyEstado(): TriviaDueloEstadoDTO
    {
        return new TriviaDueloEstadoDTO(
            partida_id: 10, codigo_sala: 'ABC123', estado_partida: 'en_curso', categoria_id: 5,
            total_preguntas: 5, mi_puntaje: 0, mi_pregunta_indice: 0, mi_estado: 'jugando',
            rival: null, pregunta_actual: null, resultado: null,
        );
    }

    private function handler(
        TriviaPartidaRepositoryInterface $partidaRepository,
        TriviaPreguntaRepositoryInterface $preguntaRepository,
        TriviaDueloEstadoService $estadoService,
    ): ResponderDueloTriviaHandler {
        $nivelRepository = \Mockery::mock(TriviaNivelRepositoryInterface::class);
        $nivelRepository->shouldReceive('findById')->with(10)->andReturn(
            new TriviaNivelDTO(id: 10, categoria_id: 5, nombre: 'Fácil', orden: 0, puntaje_base: 200, activo: true)
        );
        $motor = new TriviaMotorService($preguntaRepository, $nivelRepository);

        return new ResponderDueloTriviaHandler($partidaRepository, $preguntaRepository, $nivelRepository, $motor, $estadoService);
    }

    public function test_lanza_excepcion_si_la_partida_no_esta_en_curso(): void
    {
        $partidaRepository = \Mockery::mock(TriviaPartidaRepositoryInterface::class);
        $partidaRepository->shouldReceive('findPartidaConLock')->once()->with(10)->andReturn($this->partidaLock('finalizada', [100, 101]));

        $preguntaRepository = \Mockery::mock(TriviaPreguntaRepositoryInterface::class);
        $estadoService = \Mockery::mock(TriviaDueloEstadoService::class);

        $this->expectException(TriviaPartidaFinalizadaException::class);
        $this->handler($partidaRepository, $preguntaRepository, $estadoService)
            ->handle(new ResponderDueloTriviaCommand(partidaId: 10, usuarioId: 1, preguntaId: 100, opcionId: 100, tiempoRespuestaMs: 1000));
    }

    public function test_lanza_excepcion_si_la_pregunta_no_es_la_actual(): void
    {
        $partidaRepository = \Mockery::mock(TriviaPartidaRepositoryInterface::class);
        $partidaRepository->shouldReceive('findPartidaConLock')->once()->andReturn($this->partidaLock('en_curso', [100, 101]));
        $partidaRepository->shouldReceive('findJugador')->once()->with(10, 1)->andReturn($this->jugador(1, 100));

        $preguntaRepository = \Mockery::mock(TriviaPreguntaRepositoryInterface::class);
        $estadoService = \Mockery::mock(TriviaDueloEstadoService::class);

        $this->expectException(TriviaPreguntaInvalidaException::class);
        $this->handler($partidaRepository, $preguntaRepository, $estadoService)
            ->handle(new ResponderDueloTriviaCommand(partidaId: 10, usuarioId: 1, preguntaId: 999, opcionId: 100, tiempoRespuestaMs: 1000));
    }

    public function test_avanza_a_la_siguiente_pregunta_si_quedan_mas(): void
    {
        $partidaRepository = \Mockery::mock(TriviaPartidaRepositoryInterface::class);
        $partidaRepository->shouldReceive('findPartidaConLock')->once()->andReturn($this->partidaLock('en_curso', [100, 101, 102]));
        $partidaRepository->shouldReceive('findJugador')->once()->with(10, 1)->andReturn($this->jugador(1, 100, puntaje: 0, indice: 0));
        $partidaRepository->shouldReceive('registrarRespuesta')->once();
        $partidaRepository->shouldReceive('actualizarProgreso')->once()->with(1, [
            'puntaje' => 200, 'pregunta_indice' => 1, 'pregunta_actual_id' => 101, 'estado' => 'jugando',
        ]);
        $partidaRepository->shouldNotReceive('otroJugador');

        $preguntaRepository = \Mockery::mock(TriviaPreguntaRepositoryInterface::class);
        $preguntaRepository->shouldReceive('findById')->once()->with(100)->andReturn($this->pregunta(100));

        $estadoService = \Mockery::mock(TriviaDueloEstadoService::class);
        $estadoService->shouldReceive('construir')->once()->with(10, 1)->andReturn($this->dummyEstado());

        $resultado = $this->handler($partidaRepository, $preguntaRepository, $estadoService)
            ->handle(new ResponderDueloTriviaCommand(partidaId: 10, usuarioId: 1, preguntaId: 100, opcionId: 100, tiempoRespuestaMs: 1000));

        $this->assertInstanceOf(TriviaDueloEstadoDTO::class, $resultado);
    }

    public function test_termina_y_espera_al_rival_si_el_rival_no_ha_terminado(): void
    {
        $partidaRepository = \Mockery::mock(TriviaPartidaRepositoryInterface::class);
        $partidaRepository->shouldReceive('findPartidaConLock')->once()->andReturn($this->partidaLock('en_curso', [100, 101]));
        $partidaRepository->shouldReceive('findJugador')->once()->with(10, 1)->andReturn($this->jugador(1, 101, puntaje: 200, indice: 1));
        $partidaRepository->shouldReceive('registrarRespuesta')->once();
        $partidaRepository->shouldReceive('actualizarProgreso')->once()->with(1, [
            'puntaje' => 400, 'pregunta_indice' => 2, 'pregunta_actual_id' => null, 'estado' => 'terminado',
        ]);
        $partidaRepository->shouldReceive('otroJugador')->once()->with(10, 1)->andReturn($this->rival(2, 'jugando', 100));
        $partidaRepository->shouldNotReceive('actualizarEstadoPartida');

        $preguntaRepository = \Mockery::mock(TriviaPreguntaRepositoryInterface::class);
        $preguntaRepository->shouldReceive('findById')->once()->with(101)->andReturn($this->pregunta(101));

        $estadoService = \Mockery::mock(TriviaDueloEstadoService::class);
        $estadoService->shouldReceive('construir')->once()->with(10, 1)->andReturn($this->dummyEstado());

        $this->handler($partidaRepository, $preguntaRepository, $estadoService)
            ->handle(new ResponderDueloTriviaCommand(partidaId: 10, usuarioId: 1, preguntaId: 101, opcionId: 100, tiempoRespuestaMs: 1000));
    }

    public function test_resuelve_ganador_y_perdedor_cuando_ambos_terminan(): void
    {
        $partidaRepository = \Mockery::mock(TriviaPartidaRepositoryInterface::class);
        $partidaRepository->shouldReceive('findPartidaConLock')->once()->andReturn($this->partidaLock('en_curso', [100, 101]));
        $partidaRepository->shouldReceive('findJugador')->once()->with(10, 1)->andReturn($this->jugador(1, 101, puntaje: 200, indice: 1));
        $partidaRepository->shouldReceive('registrarRespuesta')->once();
        $partidaRepository->shouldReceive('actualizarProgreso')->once()->with(1, [
            'puntaje' => 400, 'pregunta_indice' => 2, 'pregunta_actual_id' => null, 'estado' => 'terminado',
        ]);
        $partidaRepository->shouldReceive('otroJugador')->once()->with(10, 1)->andReturn($this->rival(2, 'terminado', 300));

        $partidaRepository->shouldReceive('actualizarProgreso')->once()->with(1, ['estado' => 'ganador']);
        $partidaRepository->shouldReceive('actualizarProgreso')->once()->with(2, ['estado' => 'perdedor']);
        $partidaRepository->shouldReceive('actualizarEstadoPartida')->once()->with(10, 'finalizada');

        $preguntaRepository = \Mockery::mock(TriviaPreguntaRepositoryInterface::class);
        $preguntaRepository->shouldReceive('findById')->once()->with(101)->andReturn($this->pregunta(101));

        $estadoService = \Mockery::mock(TriviaDueloEstadoService::class);
        $estadoService->shouldReceive('construir')->once()->with(10, 1)->andReturn($this->dummyEstado());

        $this->handler($partidaRepository, $preguntaRepository, $estadoService)
            ->handle(new ResponderDueloTriviaCommand(partidaId: 10, usuarioId: 1, preguntaId: 101, opcionId: 100, tiempoRespuestaMs: 1000));
    }

    public function test_resuelve_empate_cuando_ambos_terminan_con_el_mismo_puntaje(): void
    {
        $partidaRepository = \Mockery::mock(TriviaPartidaRepositoryInterface::class);
        $partidaRepository->shouldReceive('findPartidaConLock')->once()->andReturn($this->partidaLock('en_curso', [100, 101]));
        $partidaRepository->shouldReceive('findJugador')->once()->with(10, 1)->andReturn($this->jugador(1, 101, puntaje: 200, indice: 1));
        $partidaRepository->shouldReceive('registrarRespuesta')->once();
        $partidaRepository->shouldReceive('actualizarProgreso')->once()->with(1, [
            'puntaje' => 400, 'pregunta_indice' => 2, 'pregunta_actual_id' => null, 'estado' => 'terminado',
        ]);
        $partidaRepository->shouldReceive('otroJugador')->once()->with(10, 1)->andReturn($this->rival(2, 'terminado', 400));

        $partidaRepository->shouldReceive('actualizarProgreso')->once()->with(1, ['estado' => 'empate']);
        $partidaRepository->shouldReceive('actualizarProgreso')->once()->with(2, ['estado' => 'empate']);
        $partidaRepository->shouldReceive('actualizarEstadoPartida')->once()->with(10, 'finalizada');

        $preguntaRepository = \Mockery::mock(TriviaPreguntaRepositoryInterface::class);
        $preguntaRepository->shouldReceive('findById')->once()->with(101)->andReturn($this->pregunta(101));

        $estadoService = \Mockery::mock(TriviaDueloEstadoService::class);
        $estadoService->shouldReceive('construir')->once()->with(10, 1)->andReturn($this->dummyEstado());

        $this->handler($partidaRepository, $preguntaRepository, $estadoService)
            ->handle(new ResponderDueloTriviaCommand(partidaId: 10, usuarioId: 1, preguntaId: 101, opcionId: 100, tiempoRespuestaMs: 1000));
    }
}
