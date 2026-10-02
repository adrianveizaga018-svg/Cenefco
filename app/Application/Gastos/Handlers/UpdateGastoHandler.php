<?php

namespace App\Application\Gastos\Handlers;

use App\Application\Gastos\Commands\UpdateGastoCommand;
use App\Application\Gastos\DTOs\GastoDTO;
use App\Domain\Gastos\Contracts\GastoRepositoryInterface;

class UpdateGastoHandler
{
    public function __construct(private readonly GastoRepositoryInterface $repository) {}

    public function handle(UpdateGastoCommand $c): GastoDTO
    {
        $data = array_filter([
            'categoria_gasto_id' => $c->categoria_gasto_id,
            'concepto'           => $c->concepto,
            'monto'              => $c->monto,
            'fecha'              => $c->fecha,
            'responsable'        => $c->responsable,
            'comprobante_url'    => $c->comprobante_url,
            'nota'               => $c->nota,
            'campana_publicidad_id' => $c->campana_publicidad_id,
        ], fn ($v) => $v !== null);

        return $this->repository->update($c->id, $data);
    }
}
