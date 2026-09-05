<?php

namespace App\Application\Comisiones\Handlers;

use App\Application\Comisiones\Commands\PagarComisionCommand;
use App\Application\Comisiones\DTOs\ComisionLiquidacionDTO;
use App\Domain\Comisiones\Contracts\ComisionLiquidacionRepositoryInterface;
use App\Domain\Comisiones\Exceptions\ComisionLiquidacionNotFoundException;
use App\Domain\Comisiones\Exceptions\EstadoComisionInvalidoException;

class PagarComisionHandler
{
    public function __construct(
        private readonly ComisionLiquidacionRepositoryInterface $repository,
    ) {}

    public function handle(PagarComisionCommand $command): ComisionLiquidacionDTO
    {
        $actual = $this->repository->findById($command->id);
        if (! $actual) {
            throw new ComisionLiquidacionNotFoundException($command->id);
        }
        if ($actual->estado !== 'aprobado') {
            throw new EstadoComisionInvalidoException($actual->estado, 'marcar como pagada');
        }

        $model = $this->repository->update($command->id, [
            'estado'                => 'pagado',
            'pagado_por'            => $command->pagadoPor,
            'pagado_at'             => now(),
            'comprobante_pago_url'  => $command->comprobantePagoUrl,
        ]);

        return ComisionLiquidacionDTO::fromModel($model);
    }
}
