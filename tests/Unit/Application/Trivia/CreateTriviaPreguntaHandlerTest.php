<?php

namespace Tests\Unit\Application\Trivia;

use App\Application\Trivia\Commands\CreateTriviaPreguntaCommand;
use App\Application\Trivia\DTOs\TriviaPreguntaDTO;
use App\Application\Trivia\Handlers\CreateTriviaPreguntaHandler;
use App\Domain\Trivia\Contracts\TriviaPreguntaRepositoryInterface;
use Tests\TestCase;

class CreateTriviaPreguntaHandlerTest extends TestCase
{
    public function test_crea_la_pregunta_junto_con_sus_opciones_en_una_sola_llamada(): void
    {
        $opciones = [
            ['texto' => 'París', 'es_correcta' => true, 'orden' => 0],
            ['texto' => 'Madrid', 'es_correcta' => false, 'orden' => 1],
        ];

        $dto = new TriviaPreguntaDTO(
            id: 1,
            categoria_id: 1,
            nivel_id: 1,
            enunciado: '¿Capital de Francia?',
            imagen_url: null,
            tiempo_limite_segundos: 20,
            activo: true,
            created_at: null,
            opciones: [],
        );

        $repository = \Mockery::mock(TriviaPreguntaRepositoryInterface::class);
        $repository->shouldReceive('create')
            ->once()
            ->with([
                'categoria_id' => 1,
                'nivel_id' => 1,
                'enunciado' => '¿Capital de Francia?',
                'imagen_url' => null,
                'tiempo_limite_segundos' => 20,
                'activo' => true,
            ], $opciones)
            ->andReturn($dto);

        $handler = new CreateTriviaPreguntaHandler($repository);

        $resultado = $handler->handle(new CreateTriviaPreguntaCommand(
            categoria_id: 1,
            nivel_id: 1,
            enunciado: '¿Capital de Francia?',
            imagen_url: null,
            tiempo_limite_segundos: 20,
            activo: true,
            opciones: $opciones,
        ));

        $this->assertSame($dto, $resultado);
    }
}
