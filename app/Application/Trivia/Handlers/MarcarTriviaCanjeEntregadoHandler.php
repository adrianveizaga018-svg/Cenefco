<?php

namespace App\Application\Trivia\Handlers;

use App\Application\Trivia\Commands\MarcarTriviaCanjeEntregadoCommand;
use App\Application\Trivia\DTOs\TriviaCanjeDTO;
use App\Domain\Trivia\Contracts\TriviaCanjeRepositoryInterface;
use App\Domain\Trivia\Exceptions\TriviaCanjeNotFoundException;
use App\Domain\Trivia\Exceptions\TriviaCanjeYaResueltoException;
use Illuminate\Support\Facades\DB;

class MarcarTriviaCanjeEntregadoHandler
{
    public function __construct(
        private readonly TriviaCanjeRepositoryInterface $repository
    ) {}

    public function handle(MarcarTriviaCanjeEntregadoCommand $command): TriviaCanjeDTO
    {
        return DB::transaction(function () use ($command) {
            $canje = $this->repository->findByIdConLock($command->id);
            if (! $canje) {
                throw new TriviaCanjeNotFoundException($command->id);
            }
            if ($canje->estado !== 'pendiente') {
                throw new TriviaCanjeYaResueltoException();
            }

            $canje = $this->repository->marcarEntregado($command->id, [
                'estado' => 'entregado',
                'nota' => $command->nota,
                'entregado_por' => $command->entregadoPor,
                'fecha_resolucion' => now(),
            ]);

            return TriviaCanjeDTO::fromModel($canje);
        });
    }
}
