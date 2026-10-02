<?php

namespace App\Application\Gastos\Handlers;

use App\Application\Gastos\Commands\CreateGastoRecurrenteCommand;
use App\Application\Gastos\DTOs\GastoRecurrenteDTO;
use App\Domain\Gastos\Contracts\GastoRecurrenteRepositoryInterface;

class CreateGastoRecurrenteHandler
{
    public function __construct(private readonly GastoRecurrenteRepositoryInterface $repository) {}

    public function handle(CreateGastoRecurrenteCommand $c): GastoRecurrenteDTO
    {
        return $this->repository->create([
            'categoria_gasto_id' => $c->categoria_gasto_id,
            'concepto'           => $c->concepto,
            'monto'              => $c->monto,
            'dia_del_mes'        => $c->dia_del_mes,
            'activo'             => $c->activo,
        ]);
    }
}
