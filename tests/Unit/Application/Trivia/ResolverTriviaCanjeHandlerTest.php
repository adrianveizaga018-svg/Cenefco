<?php

namespace Tests\Unit\Application\Trivia;

use App\Application\Trivia\Commands\CancelarTriviaCanjeCommand;
use App\Application\Trivia\Commands\MarcarTriviaCanjeEntregadoCommand;
use App\Application\Trivia\Handlers\CancelarTriviaCanjeHandler;
use App\Application\Trivia\Handlers\MarcarTriviaCanjeEntregadoHandler;
use App\Domain\Trivia\Contracts\TriviaCanjeRepositoryInterface;
use App\Domain\Trivia\Contracts\TriviaPremioRepositoryInterface;
use App\Domain\Trivia\Exceptions\TriviaCanjeNotFoundException;
use App\Domain\Trivia\Exceptions\TriviaCanjeYaResueltoException;
use Tests\TestCase;

class ResolverTriviaCanjeHandlerTest extends TestCase
{
    private function canje(string $estado): object
    {
        $canje = new \stdClass();
        $canje->id = 1;
        $canje->codigo = 'CNJ-2026-ABCDEF';
        $canje->estado = $estado;
        $canje->costo_puntos = 300;
        $canje->nota = null;
        $canje->created_at = now();
        $canje->fecha_resolucion = null;
        $canje->usuario_id = 1;
        $canje->premio_id = 7;

        $usuario = new \stdClass();
        $usuario->nombre = 'Alfredo';
        $usuario->apellido = 'Callizaya';
        $usuario->email = 'alfredo@test.com';
        $canje->usuario = $usuario;

        $premio = new \stdClass();
        $premio->nombre = 'Termo CENEFCO';
        $premio->tipo = 'souvenir';
        $premio->stock = 4;
        $canje->premio = $premio;

        return $canje;
    }

    public function test_marcar_entregado_lanza_excepcion_si_no_existe(): void
    {
        $repository = \Mockery::mock(TriviaCanjeRepositoryInterface::class);
        $repository->shouldReceive('findByIdConLock')->once()->with(1)->andReturnNull();

        $handler = new MarcarTriviaCanjeEntregadoHandler($repository);

        $this->expectException(TriviaCanjeNotFoundException::class);
        $handler->handle(new MarcarTriviaCanjeEntregadoCommand(id: 1, entregadoPor: 2, nota: null));
    }

    public function test_marcar_entregado_lanza_excepcion_si_ya_fue_resuelto(): void
    {
        $repository = \Mockery::mock(TriviaCanjeRepositoryInterface::class);
        $repository->shouldReceive('findByIdConLock')->once()->with(1)->andReturn($this->canje('entregado'));
        $repository->shouldNotReceive('marcarEntregado');

        $handler = new MarcarTriviaCanjeEntregadoHandler($repository);

        $this->expectException(TriviaCanjeYaResueltoException::class);
        $handler->handle(new MarcarTriviaCanjeEntregadoCommand(id: 1, entregadoPor: 2, nota: null));
    }

    public function test_marcar_entregado_actualiza_estado(): void
    {
        $repository = \Mockery::mock(TriviaCanjeRepositoryInterface::class);
        $repository->shouldReceive('findByIdConLock')->once()->with(1)->andReturn($this->canje('pendiente'));
        $repository->shouldReceive('marcarEntregado')->once()->andReturn($this->canje('entregado'));

        $handler = new MarcarTriviaCanjeEntregadoHandler($repository);
        $resultado = $handler->handle(new MarcarTriviaCanjeEntregadoCommand(id: 1, entregadoPor: 2, nota: 'Retirado en secretaría'));

        $this->assertSame('entregado', $resultado->estado);
    }

    public function test_cancelar_repone_el_stock_del_premio(): void
    {
        $canjeRepository = \Mockery::mock(TriviaCanjeRepositoryInterface::class);
        $canjeRepository->shouldReceive('findByIdConLock')->once()->with(1)->andReturn($this->canje('pendiente'));
        $canjeRepository->shouldReceive('cancelar')->once()->andReturn($this->canje('cancelado'));

        $premioRepository = \Mockery::mock(TriviaPremioRepositoryInterface::class);
        $premio = new \stdClass();
        $premio->id = 7;
        $premio->stock = 4;
        $premioRepository->shouldReceive('findById')->once()->with(7)->andReturn($premio);
        $premioRepository->shouldReceive('incrementarStock')->once()->with(7);

        $handler = new CancelarTriviaCanjeHandler($canjeRepository, $premioRepository);
        $resultado = $handler->handle(new CancelarTriviaCanjeCommand(id: 1, canceladoPor: 2, nota: 'Reclamo del cliente'));

        $this->assertSame('cancelado', $resultado->estado);
    }
}
