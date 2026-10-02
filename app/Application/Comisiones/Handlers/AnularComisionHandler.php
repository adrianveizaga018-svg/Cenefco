<?php

namespace App\Application\Comisiones\Handlers;

use App\Application\Comisiones\Commands\AnularComisionCommand;
use App\Application\Comisiones\DTOs\ComisionLiquidacionDTO;
use App\Domain\Comisiones\Contracts\ComisionLiquidacionRepositoryInterface;
use App\Domain\Comisiones\Exceptions\ComisionLiquidacionNotFoundException;
use App\Domain\Comisiones\Exceptions\EstadoComisionInvalidoException;
use Illuminate\Support\Facades\DB;

class AnularComisionHandler
{
    public function __construct(
        private readonly ComisionLiquidacionRepositoryInterface $repository,
    ) {}

    public function handle(AnularComisionCommand $command): ComisionLiquidacionDTO
    {
        $actual = $this->repository->findById($command->id);
        if (! $actual) {
            throw new ComisionLiquidacionNotFoundException($command->id);
        }
        if (! in_array($actual->estado, ['calculado', 'aprobado'], true)) {
            throw new EstadoComisionInvalidoException($actual->estado, 'anular');
        }

        return DB::transaction(function () use ($command) {
            $this->repository->eliminarDetalle($command->id);

            $model = $this->repository->update($command->id, [
                'estado' => 'anulado',
                'nota'   => $command->nota,
            ]);

            return ComisionLiquidacionDTO::fromModel($model);
        });
    }
}
