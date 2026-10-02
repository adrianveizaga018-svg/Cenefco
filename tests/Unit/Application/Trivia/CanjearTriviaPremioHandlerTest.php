<?php

namespace Tests\Unit\Application\Trivia;

use App\Application\Trivia\Commands\CanjearTriviaPremioCommand;
use App\Application\Trivia\Handlers\CanjearTriviaPremioHandler;
use App\Application\Trivia\Services\TriviaSaldoService;
use App\Domain\Trivia\Contracts\TriviaCanjeRepositoryInterface;
use App\Domain\Trivia\Contracts\TriviaPartidaRepositoryInterface;
use App\Domain\Trivia\Contracts\TriviaPremioRepositoryInterface;
use App\Domain\Trivia\Exceptions\TriviaPremioNotFoundException;
use App\Domain\Trivia\Exceptions\TriviaSaldoInsuficienteException;
use App\Domain\Trivia\Exceptions\TriviaStockAgotadoException;
use Tests\TestCase;

class CanjearTriviaPremioHandlerTest extends TestCase
{
    private function premio(int $id, int $costoPuntos, ?int $stock, bool $activo = true): object
    {
        $premio = new \stdClass();
        $premio->id = $id;
        $premio->costo_puntos = $costoPuntos;
        $premio->stock = $stock;
        $premio->activo = $activo;

        return $premio;
    }

    private function canje(): object
    {
        $canje = new \stdClass();
        $canje->id = 1;
        $canje->codigo = 'CNJ-2026-ABCDEF';
        $canje->estado = 'pendiente';
        $canje->costo_puntos = 300;
        $canje->nota = null;
        $canje->created_at = now();
        $canje->premio_id = 7;

        $premio = new \stdClass();
        $premio->nombre = 'Termo CENEFCO';
        $premio->tipo = 'souvenir';
        $premio->imagen_url = null;
        $canje->premio = $premio;

        return $canje;
    }

    public function test_lanza_excepcion_si_el_premio_no_existe_o_esta_inactivo(): void
    {
        $premioRepository = \Mockery::mock(TriviaPremioRepositoryInterface::class);
        $premioRepository->shouldReceive('findByIdConLock')->once()->with(7)->andReturnNull();

        $canjeRepository = \Mockery::mock(TriviaCanjeRepositoryInterface::class);
        $canjeRepository->shouldNotReceive('create');

        $saldoService = new TriviaSaldoService(
            \Mockery::mock(TriviaPartidaRepositoryInterface::class),
            $canjeRepository
        );

        $handler = new CanjearTriviaPremioHandler($premioRepository, $canjeRepository, $saldoService);

        $this->expectException(TriviaPremioNotFoundException::class);
        $handler->handle(new CanjearTriviaPremioCommand(usuarioId: 1, premioId: 7));
    }

    public function test_lanza_excepcion_si_no_hay_stock(): void
    {
        $premioRepository = \Mockery::mock(TriviaPremioRepositoryInterface::class);
        $premioRepository->shouldReceive('findByIdConLock')->once()->with(7)->andReturn($this->premio(7, 300, 0));

        $canjeRepository = \Mockery::mock(TriviaCanjeRepositoryInterface::class);
        $canjeRepository->shouldNotReceive('create');

        $saldoService = new TriviaSaldoService(
            \Mockery::mock(TriviaPartidaRepositoryInterface::class),
            $canjeRepository
        );

        $handler = new CanjearTriviaPremioHandler($premioRepository, $canjeRepository, $saldoService);

        $this->expectException(TriviaStockAgotadoException::class);
        $handler->handle(new CanjearTriviaPremioCommand(usuarioId: 1, premioId: 7));
    }

    public function test_lanza_excepcion_si_el_saldo_es_insuficiente(): void
    {
        $premioRepository = \Mockery::mock(TriviaPremioRepositoryInterface::class);
        $premioRepository->shouldReceive('findByIdConLock')->once()->with(7)->andReturn($this->premio(7, 1000, null));

        $partidaRepository = \Mockery::mock(TriviaPartidaRepositoryInterface::class);
        $partidaRepository->shouldReceive('puntajeTotalUsuario')->once()->with(1)->andReturn(800);

        $canjeRepository = \Mockery::mock(TriviaCanjeRepositoryInterface::class);
        $canjeRepository->shouldReceive('puntosGastadosUsuario')->once()->with(1)->andReturn(0);
        $canjeRepository->shouldNotReceive('create');

        $saldoService = new TriviaSaldoService($partidaRepository, $canjeRepository);
        $handler = new CanjearTriviaPremioHandler($premioRepository, $canjeRepository, $saldoService);

        $this->expectException(TriviaSaldoInsuficienteException::class);
        $handler->handle(new CanjearTriviaPremioCommand(usuarioId: 1, premioId: 7));
    }

    public function test_canjea_correctamente_y_descuenta_stock(): void
    {
        $premioRepository = \Mockery::mock(TriviaPremioRepositoryInterface::class);
        $premioRepository->shouldReceive('findByIdConLock')->once()->with(7)->andReturn($this->premio(7, 300, 5));
        $premioRepository->shouldReceive('decrementarStock')->once()->with(7);

        $partidaRepository = \Mockery::mock(TriviaPartidaRepositoryInterface::class);
        $partidaRepository->shouldReceive('puntajeTotalUsuario')->once()->with(1)->andReturn(800);

        $canjeRepository = \Mockery::mock(TriviaCanjeRepositoryInterface::class);
        $canjeRepository->shouldReceive('puntosGastadosUsuario')->once()->with(1)->andReturn(0);
        $canjeRepository->shouldReceive('create')->once()->with([
            'usuario_id' => 1,
            'premio_id' => 7,
            'costo_puntos' => 300,
            'estado' => 'pendiente',
        ])->andReturn($this->canje());

        $saldoService = new TriviaSaldoService($partidaRepository, $canjeRepository);
        $handler = new CanjearTriviaPremioHandler($premioRepository, $canjeRepository, $saldoService);

        $resultado = $handler->handle(new CanjearTriviaPremioCommand(usuarioId: 1, premioId: 7));

        $this->assertSame('CNJ-2026-ABCDEF', $resultado->codigo);
        $this->assertSame('pendiente', $resultado->estado);
        $this->assertSame(300, $resultado->costo_puntos);
    }
}
