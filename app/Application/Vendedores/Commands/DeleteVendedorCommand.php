<?php

namespace App\Application\Vendedores\Commands;

final readonly class DeleteVendedorCommand
{
    public function __construct(public int $id) {}
}
