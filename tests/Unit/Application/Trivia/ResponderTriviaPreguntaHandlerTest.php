<?php

namespace Tests\Unit\Application\Trivia;

use App\Application\Trivia\Commands\ResponderTriviaPreguntaCommand;
use App\Application\Trivia\DTOs\TriviaNivelDTO;
use App\Application\Trivia\DTOs\TriviaOpcionDTO;
use App\Application\Trivia\DTOs\TriviaPreguntaDTO;
use App\Application\Trivia\Handlers\ResponderTriviaPreguntaHandler;
use App\Application\Trivia\Services\TriviaMotorService;
use App\Domain\Trivia\Contracts\TriviaNivelRepositoryInterface;
use App\Domain\Trivia\Contracts\TriviaPartidaRepositoryInterface;
use App\Domain\Trivia\Contracts\TriviaPreguntaRepositoryInterface;
use App\Domain\Trivia\Exceptions\TriviaPreguntaInvalidaException;
use Tests\TestCase;

class ResponderTriviaPreguntaHandlerTest extends TestCase
{
    private function pregunta(int $id, int $nivelId = 10): TriviaPreguntaDTO
    {
        return new TriviaPreguntaDTO(
            id: $id,
            categoria_id: 5,
            nivel_id: $nivelId,
            enunciado: '¿Pregunta?',
            imagen_url: null,
            tiempo_limite_segundos: 20,
            activo: true,
            created_at: null,
            opciones: [
                new TriviaOpcionDTO(id: 100, texto: 'Correcta', es_correcta: true, orden: 0),
                new TriviaOpcionDTO(id: 101, texto: 'Incorrecta', es_correcta: false, orden: 1),
            ],
        );
    }

    private function jugador(int $puntaje, int $vidas, int $preguntaActualId): object
    {
        $jugador = new \stdClass();
        $jugador->id = 9;
        $jugador->partida_id = 3;
        $jugador->puntaje = $puntaje;
        $jugador->vidas = $vidas;
        $jugador->estado = 'jugando';
        $jugador->pregunta_actual_id = $preguntaActualId;

        $partida = new \stdClass();
        $partida->categoria_id = 5;
        $partida->estado = 'en_curso';
        $jugador->partida = $partida;

        return $jugador;
    }

    public function test_rechaza_si_la_pregunta_enviada_no_es_la_pregunta_actual(): void
    {
        $partidaRepository = \Mockery::mock(TriviaPartidaRepositoryInterface::class);
        $partidaRepository->shouldReceive('findJugador')->once()->with(3, 1)
            ->andReturn($this->jugador(puntaje: 0, vidas: 3, preguntaActualId: 55));
        $partidaRepository->shouldNotReceive('registrarRespuesta');

        $handler = new ResponderTriviaPreguntaHandler(
            $partidaRepository,
            \Mockery::mock(TriviaPreguntaRepositoryInterface::class),
            \Mockery::mock(TriviaNivelRepositoryInterface::class),
            \Mockery::mock(TriviaMotorService::class),
        );

        $this->expectException(TriviaPreguntaInvalidaException::class);
        $handler->handle(new ResponderTriviaPreguntaCommand(
            partidaId: 3, usuarioId: 1, preguntaId: 999, opcionId: 100, tiempoRespuestaMs: 1000,
        ));
    }

    public function test_respuesta_correcta_suma_el_puntaje_base_del_nivel_y_sirve_la_siguiente_pregunta(): void
    {
        $preguntaActual = $this->pregunta(id: 55, nivelId: 10);
        $preguntaSiguiente = $this->pregunta(id: 56, nivelId: 10);

        $jugadorInicial = $this->jugador(puntaje: 0, vidas: 3, preguntaActualId: 55);
        $jugadorFinal = $this->jugador(puntaje: 200, vidas: 3, preguntaActualId: 56);

        $partidaRepository = \Mockery::mock(TriviaPartidaRepositoryInterface::class);
        $partidaRepository->shouldReceive('findJugador')->twice()->with(3, 1)
            ->andReturn($jugadorInicial, $jugadorFinal);
        $partidaRepository->shouldReceive('registrarRespuesta')->once()
            ->with(\Mockery::on(fn ($data) => $data['es_correcta'] === true && $data['opcion_id'] === 100));
        $partidaRepository->shouldReceive('preguntasRespondidasIds')->once()->with(3)->andReturn([55]);
        $partidaRepository->shouldReceive('actualizarProgreso')->once()
            ->with(9, ['puntaje' => 200, 'vidas' => 3, 'estado' => 'jugando', 'pregunta_actual_id' => 56]);
        $partidaRepository->shouldNotReceive('actualizarEstadoPartida');

        $preguntaRepository = \Mockery::mock(TriviaPreguntaRepositoryInterface::class);
        $preguntaRepository->shouldReceive('findById')->once()->with(55)->andReturn($preguntaActual);
        $preguntaRepository->shouldReceive('siguienteParaJuego')->once()->with(5, [55])->andReturn($preguntaSiguiente);

        $nivelRepository = \Mockery::mock(TriviaNivelRepositoryInterface::class);
        $nivelRepository->shouldReceive('findById')->once()->with(10)
            ->andReturn(new TriviaNivelDTO(id: 10, categoria_id: 5, nombre: 'Difícil', orden: 1, puntaje_base: 200, activo: true));

        $motor = new TriviaMotorService($preguntaRepository, $nivelRepository);
        $handler = new ResponderTriviaPreguntaHandler($partidaRepository, $preguntaRepository, $nivelRepository, $motor);

        $resultado = $handler->handle(new ResponderTriviaPreguntaCommand(
            partidaId: 3, usuarioId: 1, preguntaId: 55, opcionId: 100, tiempoRespuestaMs: 1500,
        ));

        $this->assertTrue($resultado['es_correcta']);
        $this->assertSame(200, $resultado['partida']->puntaje);
        $this->assertSame(56, $resultado['pregunta']->id);
    }

    public function test_agotar_las_vidas_finaliza_la_partida_como_perdedor(): void
    {
        $preguntaActual = $this->pregunta(id: 55, nivelId: 10);
        $jugadorInicial = $this->jugador(puntaje: 100, vidas: 1, preguntaActualId: 55);
        $jugadorFinal = $this->jugador(puntaje: 100, vidas: 0, preguntaActualId: 55);
        $jugadorFinal->estado = 'perdedor';
        $jugadorFinal->partida->estado = 'finalizada';

        $partidaRepository = \Mockery::mock(TriviaPartidaRepositoryInterface::class);
        $partidaRepository->shouldReceive('findJugador')->twice()->with(3, 1)
            ->andReturn($jugadorInicial, $jugadorFinal);
        $partidaRepository->shouldReceive('registrarRespuesta')->once();
        $partidaRepository->shouldNotReceive('preguntasRespondidasIds');
        $partidaRepository->shouldReceive('actualizarProgreso')->once()
            ->with(9, ['puntaje' => 100, 'vidas' => 0, 'estado' => 'perdedor', 'pregunta_actual_id' => null]);
        $partidaRepository->shouldReceive('actualizarEstadoPartida')->once()->with(3, 'finalizada');

        $preguntaRepository = \Mockery::mock(TriviaPreguntaRepositoryInterface::class);
        $preguntaRepository->shouldReceive('findById')->once()->with(55)->andReturn($preguntaActual);

        $nivelRepository = \Mockery::mock(TriviaNivelRepositoryInterface::class);

        $motor = new TriviaMotorService($preguntaRepository, $nivelRepository);
        $handler = new ResponderTriviaPreguntaHandler($partidaRepository, $preguntaRepository, $nivelRepository, $motor);

        $resultado = $handler->handle(new ResponderTriviaPreguntaCommand(
            partidaId: 3, usuarioId: 1, preguntaId: 55, opcionId: 101, tiempoRespuestaMs: 5000,
        ));

        $this->assertFalse($resultado['es_correcta']);
        $this->assertSame('finalizada', $resultado['partida']->estado_partida);
        $this->assertSame('perdedor', $resultado['partida']->estado_jugador);
        $this->assertNull($resultado['pregunta']);
    }
}
