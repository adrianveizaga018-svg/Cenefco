<?php

namespace Tests\Unit\Application\Trivia;

use App\Application\Trivia\Commands\CreateTriviaCategoriaCommand;
use App\Application\Trivia\DTOs\TriviaCategoriaDTO;
use App\Application\Trivia\Handlers\CreateTriviaCategoriaHandler;
use App\Domain\Trivia\Contracts\TriviaCategoriaRepositoryInterface;
use Tests\TestCase;

class CreateTriviaCategoriaHandlerTest extends TestCase
{
    public function test_delega_los_datos_del_command_al_repositorio(): void
    {
        $dto = new TriviaCategoriaDTO(
            id: 1,
            nombre: 'Historia',
            slug: 'historia',
            descripcion: null,
            imagen_url: null,
            color: null,
            curso_id: null,
            orden: 0,
            activo: true,
            created_at: null,
        );

        $repository = \Mockery::mock(TriviaCategoriaRepositoryInterface::class);
        $repository->shouldReceive('create')
            ->once()
            ->with([
                'nombre' => 'Historia',
                'descripcion' => null,
                'imagen_url' => null,
                'color' => null,
                'curso_id' => null,
                'orden' => 0,
                'activo' => true,
            ])
            ->andReturn($dto);

        $handler = new CreateTriviaCategoriaHandler($repository);

        $resultado = $handler->handle(new CreateTriviaCategoriaCommand(nombre: 'Historia'));

        $this->assertSame($dto, $resultado);
    }
}
