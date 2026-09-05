<?php

namespace App\Application\Vendedores\Handlers;

use App\Application\Vendedores\Commands\UpdateVendedorCommand;
use App\Application\Vendedores\DTOs\VendedorDTO;
use App\Domain\Vendedores\Contracts\VendedorRepositoryInterface;

class UpdateVendedorHandler
{
    public function __construct(private readonly VendedorRepositoryInterface $repository) {}

    public function handle(UpdateVendedorCommand $command): VendedorDTO
    {
        return $this->repository->update($command->id, $command->toArray());
    }
}
