<?php

namespace App\Application\Vendedores\Queries;

final readonly class GetVendedorComisionDetalleQuery
{
    public function __construct(public int $vendedorId) {}
}
