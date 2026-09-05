<?php

namespace App\Application\Gastos\Handlers;

use App\Application\Gastos\Commands\CreateCategoriaGastoCommand;
use App\Application\Gastos\DTOs\CategoriaGastoDTO;
use App\Domain\Gastos\Contracts\CategoriaGastoRepositoryInterface;

class CreateCategoriaGastoHandler
{
    public function __construct(private readonly CategoriaGastoRepositoryInterface $repository) {}

    public function handle(CreateCategoriaGastoCommand $c): CategoriaGastoDTO
    {
        return $this->repository->create([
            'nombre'        => $c->nombre,
            'linea_negocio' => $c->linea_negocio,
            'activo'        => $c->activo,
        ]);
    }
}
