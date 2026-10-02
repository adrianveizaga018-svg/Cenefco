<?php

namespace App\Application\Gastos\Handlers;

use App\Application\Gastos\Commands\CreateGastoCommand;
use App\Application\Gastos\DTOs\GastoDTO;
use App\Domain\Gastos\Contracts\GastoRepositoryInterface;

class CreateGastoHandler
{
    public function __construct(private readonly GastoRepositoryInterface $repository) {}

    public function handle(CreateGastoCommand $c): GastoDTO
    {
        return $this->repository->create([
            'categoria_gasto_id'  => $c->categoria_gasto_id,
            'concepto'            => $c->concepto,
            'monto'               => $c->monto,
            'fecha'               => $c->fecha,
            'responsable'         => $c->responsable,
            'comprobante_url'     => $c->comprobante_url,
            'nota'                => $c->nota,
            'gasto_recurrente_id' => $c->gasto_recurrente_id,
            'campana_publicidad_id' => $c->campana_publicidad_id,
        ]);
    }
}
