<?php

namespace App\Application\Comisiones\Handlers;

use App\Application\Comisiones\Commands\AprobarComisionCommand;
use App\Application\Comisiones\DTOs\ComisionLiquidacionDTO;
use App\Domain\Comisiones\Contracts\ComisionLiquidacionRepositoryInterface;
use App\Domain\Comisiones\Exceptions\ComisionLiquidacionNotFoundException;
use App\Domain\Comisiones\Exceptions\EstadoComisionInvalidoException;

class AprobarComisionHandler
{
    public function __construct(
        private readonly ComisionLiquidacionRepositoryInterface $repository,
    ) {}

    public function handle(AprobarComisionCommand $command): ComisionLiquidacionDTO
    {
        $actual = $this->repository->findById($command->id);
        if (! $actual) {
            throw new ComisionLiquidacionNotFoundException($command->id);
        }
        if ($actual->estado !== 'calculado') {
            throw new EstadoComisionInvalidoException($actual->estado, 'aprobar');
        }

        $model = $this->repository->update($command->id, [
            'estado'       => 'aprobado',
            'aprobado_por' => $command->aprobadoPor,
            'aprobado_at'  => now(),
        ]);

        return ComisionLiquidacionDTO::fromModel($model);
    }
}
