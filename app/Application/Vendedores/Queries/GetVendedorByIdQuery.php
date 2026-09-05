<?php

namespace App\Application\Vendedores\Queries;

final readonly class GetVendedorByIdQuery
{
    public function __construct(public int $id) {}
}
