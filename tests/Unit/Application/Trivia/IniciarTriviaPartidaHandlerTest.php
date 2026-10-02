<?php

namespace Tests\Unit\Application\Trivia;

use App\Application\Trivia\Commands\IniciarTriviaPartidaCommand;
use App\Application\Trivia\DTOs\TriviaOpcionDTO;
use App\Application\Trivia\DTOs\TriviaPreguntaDTO;
use App\Application\Trivia\Handlers\IniciarTriviaPartidaHandler;
use App\Application\Trivia\Services\TriviaMotorService;
use App\Domain\Trivia\Contracts\TriviaNivelRepositoryInterface;
use App\Domain\Trivia\Contracts\TriviaPartidaRepositoryInterface;
use App\Domain\Trivia\Contracts\TriviaPreguntaRepositoryInterface;
use App\Domain\Trivia\Exceptions\TriviaSinPreguntasDisponiblesException;
use Tests\TestCase;

class IniciarTriviaPartidaHandlerTest extends TestCase
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
                new TriviaOpcionDTO(id: 100, texto: 'A', es_correcta: true, orden: 0),
                new TriviaOpcionDTO(id: 101, texto: 'B', es_correcta: false, orden: 1),
            ],
        );
    }

    private function jugador(int $id, int $partidaId, int $categoriaId, ?int $preguntaActualId): object
    {
        $jugador = new \stdClass();
        $jugador->id = $id;
        $jugador->partida_id = $partidaId;
        $jugador->puntaje = 0;
        $jugador->vidas = 3;
        $jugador->estado = 'jugando';
        $jugador->pregunta_actual_id = $preguntaActualId;

        $partida = new \stdClass();
        $partida->categoria_id = $categoriaId;
        $partida->estado = 'en_curso';
        $jugador->partida = $partida;

        return $jugador;
    }

    public function test_lanza_excepcion_si_la_categoria_no_tiene_preguntas(): void
    {
        $partidaRepository = \Mockery::mock(TriviaPartidaRepositoryInterface::class);
        $partidaRepository->shouldNotReceive('crearPartida');

        $preguntaRepository = \Mockery::mock(TriviaPreguntaRepositoryInterface::class);
        $preguntaRepository->shouldReceive('siguienteParaJuego')->once()->with(5, [])->andReturnNull();

        $motor = new TriviaMotorService($preguntaRepository, \Mockery::mock(TriviaNivelRepositoryInterface::class));
        $handler = new IniciarTriviaPartidaHandler($partidaRepository, $motor);

        $this->expectException(TriviaSinPreguntasDisponiblesException::class);
        $handler->handle(new IniciarTriviaPartidaCommand(categoriaId: 5, usuarioId: 1));
    }

    public function test_crea_la_partida_y_devuelve_la_primera_pregunta_sin_revelar_la_correcta(): void
    {
        $pregunta = $this->pregunta(id: 55);
        $jugadorCreado = $this->jugador(id: 9, partidaId: 3, categoriaId: 5, preguntaActualId: null);
        $jugadorFinal = $this->jugador(id: 9, partidaId: 3, categoriaId: 5, preguntaActualId: 55);

        $preguntaRepository = \Mockery::mock(TriviaPreguntaRepositoryInterface::class);
        $preguntaRepository->shouldReceive('siguienteParaJuego')->once()->with(5, [])->andReturn($pregunta);

        $partidaRepository = \Mockery::mock(TriviaPartidaRepositoryInterface::class);
        $partidaRepository->shouldReceive('crearPartida')->once()->with(5, 1)->andReturn($jugadorCreado);
        $partidaRepository->shouldReceive('actualizarProgreso')->once()->with(9, ['pregunta_actual_id' => 55]);
        $partidaRepository->shouldReceive('findJugador')->once()->with(3, 1)->andReturn($jugadorFinal);

        $motor = new TriviaMotorService($preguntaRepository, \Mockery::mock(TriviaNivelRepositoryInterface::class));
        $handler = new IniciarTriviaPartidaHandler($partidaRepository, $motor);

        $resultado = $handler->handle(new IniciarTriviaPartidaCommand(categoriaId: 5, usuarioId: 1));

        $this->assertSame(3, $resultado['partida']->partida_id);
        $this->assertSame(55, $resultado['pregunta']->id);
        $this->assertSame(['id' => 100, 'texto' => 'A'], $resultado['pregunta']->opciones[0]);
        $this->assertArrayNotHasKey('es_correcta', $resultado['pregunta']->opciones[0]);
    }
}
