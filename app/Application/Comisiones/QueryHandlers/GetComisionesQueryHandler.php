<?php

namespace App\Application\Comisiones\QueryHandlers;

use App\Application\Comisiones\Queries\GetComisionesQuery;
use App\Domain\Comisiones\Contracts\ComisionLiquidacionRepositoryInterface;

class GetComisionesQueryHandler
{
    public function __construct(
        private readonly ComisionLiquidacionRepositoryInterface $repository,
    ) {}

    public function handle(GetComisionesQuery $query): array
    {
        return $this->repository->paginate($query->pagination, [
            'vendedor_id' => $query->vendedorId,
            'estado'      => $query->estado,
        ]);
    }
}
