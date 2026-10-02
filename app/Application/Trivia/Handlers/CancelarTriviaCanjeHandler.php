<?php

namespace App\Application\Trivia\Handlers;

use App\Application\Trivia\Commands\CancelarTriviaCanjeCommand;
use App\Application\Trivia\DTOs\TriviaCanjeDTO;
use App\Domain\Trivia\Contracts\TriviaCanjeRepositoryInterface;
use App\Domain\Trivia\Contracts\TriviaPremioRepositoryInterface;
use App\Domain\Trivia\Exceptions\TriviaCanjeNotFoundException;
use App\Domain\Trivia\Exceptions\TriviaCanjeYaResueltoException;
use Illuminate\Support\Facades\DB;

class CancelarTriviaCanjeHandler
{
    public function __construct(
        private readonly TriviaCanjeRepositoryInterface $canjeRepository,
        private readonly TriviaPremioRepositoryInterface $premioRepository,
    ) {}

    public function handle(CancelarTriviaCanjeCommand $command): TriviaCanjeDTO
    {
        return DB::transaction(function () use ($command) {
            $canje = $this->canjeRepository->findByIdConLock($command->id);
            if (! $canje) {
                throw new TriviaCanjeNotFoundException($command->id);
            }
            if ($canje->estado !== 'pendiente') {
                throw new TriviaCanjeYaResueltoException();
            }

            $canje = $this->canjeRepository->cancelar($command->id, [
                'estado' => 'cancelado',
                'nota' => $command->nota,
                'entregado_por' => $command->canceladoPor,
                'fecha_resolucion' => now(),
            ]);

            $premio = $this->premioRepository->findById($canje->premio_id);
            if ($premio && $premio->stock !== null) {
                $this->premioRepository->incrementarStock($premio->id);
            }

            return TriviaCanjeDTO::fromModel($canje);
        });
    }
}
