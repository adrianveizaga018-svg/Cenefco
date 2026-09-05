<?php

namespace App\Application\Vendedores\Handlers;

use App\Application\Vendedores\Commands\DeleteVendedorCommand;
use App\Domain\Vendedores\Contracts\VendedorRepositoryInterface;

class DeleteVendedorHandler
{
    public function __construct(private readonly VendedorRepositoryInterface $repository) {}

    public function handle(DeleteVendedorCommand $command): bool
    {
        return $this->repository->delete($command->id);
    }
}
