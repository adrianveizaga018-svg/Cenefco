<?php

namespace App\Application\Ventas\QueryHandlers;

use App\Application\Ventas\Queries\GetVentasQuery;
use App\Domain\Ventas\Contracts\VentaRepositoryInterface;

class GetVentasQueryHandler
{
    public function __construct(
        private readonly VentaRepositoryInterface $repository,
    ) {}

    public function handle(GetVentasQuery $query): array
    {
        return $this->repository->paginate($query->pagination, [
            'query'        => $query->pagination->query,
            'estado_pago'  => $query->estadoPago,
            'periodo'      => $query->periodo,
            'gestion'      => $query->gestion,
            'conInactivos' => $query->conInactivos,
            'canal_venta'  => $query->canalVenta,
            'id_vendedor'  => $query->idVendedor,
        ]);
    }
}
