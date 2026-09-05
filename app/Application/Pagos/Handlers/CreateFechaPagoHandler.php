<?php

namespace App\Application\Pagos\Handlers;

use App\Application\Pagos\Commands\CreateFechaPagoCommand;
use App\Application\Pagos\DTOs\FechaPagoDTO;
use App\Domain\Pagos\Contracts\FechaPagoRepositoryInterface;

class CreateFechaPagoHandler
{
    public function __construct(
        private readonly FechaPagoRepositoryInterface $repository,
    ) {}

    public function handle(CreateFechaPagoCommand $command): FechaPagoDTO
    {
        $model = $this->repository->create([
            'id_us_reg'     => $command->idUsReg,
            'id_plan'       => $command->idPlan,
            'nro_pago'      => $command->nroPago,
            'monto_a_pagar' => $command->montoAPagar,
            'fecha_inicio'  => $command->fechaInicio,
            'fecha_fin'     => $command->fechaFin,
            'obligatorio'   => $command->obligatorio,
            'tipo_tramite'  => $command->tipoTramite,
            'estado'        => 1,
            'fecha_reg'     => now(),
        ]);

        return FechaPagoDTO::fromModel($model);
    }
}
