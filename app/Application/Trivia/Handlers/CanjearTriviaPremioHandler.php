<?php

namespace App\Application\Trivia\Handlers;

use App\Application\Trivia\Commands\CanjearTriviaPremioCommand;
use App\Application\Trivia\DTOs\TriviaCanjeUsuarioDTO;
use App\Application\Trivia\Services\TriviaSaldoService;
use App\Domain\Trivia\Contracts\TriviaCanjeRepositoryInterface;
use App\Domain\Trivia\Contracts\TriviaPremioRepositoryInterface;
use App\Domain\Trivia\Exceptions\TriviaPremioNotFoundException;
use App\Domain\Trivia\Exceptions\TriviaSaldoInsuficienteException;
use App\Domain\Trivia\Exceptions\TriviaStockAgotadoException;
use Illuminate\Support\Facades\DB;

class CanjearTriviaPremioHandler
{
    public function __construct(
        private readonly TriviaPremioRepositoryInterface $premioRepository,
        private readonly TriviaCanjeRepositoryInterface $canjeRepository,
        private readonly TriviaSaldoService $saldoService,
    ) {}

    public function handle(CanjearTriviaPremioCommand $command): TriviaCanjeUsuarioDTO
    {
        return DB::transaction(function () use ($command) {
            $premio = $this->premioRepository->findByIdConLock($command->premioId);
            if (! $premio || ! $premio->activo) {
                throw new TriviaPremioNotFoundException($command->premioId);
            }

            if ($premio->stock !== null && (int) $premio->stock <= 0) {
                throw new TriviaStockAgotadoException();
            }

            $saldo = $this->saldoService->calcular($command->usuarioId);
            if ($saldo->saldo_disponible < (int) $premio->costo_puntos) {
                throw new TriviaSaldoInsuficienteException($saldo->saldo_disponible, (int) $premio->costo_puntos);
            }

            $canje = $this->canjeRepository->create([
                'usuario_id' => $command->usuarioId,
                'premio_id' => $premio->id,
                'costo_puntos' => $premio->costo_puntos,
                'estado' => 'pendiente',
            ]);

            if ($premio->stock !== null) {
                $this->premioRepository->decrementarStock($premio->id);
            }

            return TriviaCanjeUsuarioDTO::fromModel($canje);
        });
    }
}
