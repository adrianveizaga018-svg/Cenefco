<?php

namespace App\Application\Comisiones\QueryHandlers;

use App\Application\Comisiones\DTOs\ComisionLiquidacionDetalleDTO;
use App\Application\Comisiones\DTOs\ComisionLiquidacionDTO;
use App\Application\Comisiones\Queries\GetComisionByIdQuery;
use App\Domain\Comisiones\Contracts\ComisionLiquidacionRepositoryInterface;
use App\Domain\Comisiones\Exceptions\ComisionLiquidacionNotFoundException;

class GetComisionByIdQueryHandler
{
    public function __construct(
        private readonly ComisionLiquidacionRepositoryInterface $repository,
    ) {}

    public function handle(GetComisionByIdQuery $query): ComisionLiquidacionDTO
    {
        $model = $this->repository->findById($query->id);
        if (! $model) {
            throw new ComisionLiquidacionNotFoundException($query->id);
        }

        $detalle = collect($model->detalle ?? [])
            ->map(fn ($d) => ComisionLiquidacionDetalleDTO::fromModel($d))
            ->all();

        return ComisionLiquidacionDTO::fromModel($model, $detalle);
    }
}
