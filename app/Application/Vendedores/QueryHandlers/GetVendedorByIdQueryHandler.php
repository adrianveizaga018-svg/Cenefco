<?php

namespace App\Application\Vendedores\QueryHandlers;

use App\Application\Vendedores\DTOs\VendedorDTO;
use App\Application\Vendedores\Queries\GetVendedorByIdQuery;
use App\Domain\Vendedores\Contracts\VendedorRepositoryInterface;

class GetVendedorByIdQueryHandler
{
    public function __construct(private readonly VendedorRepositoryInterface $repository) {}

    public function handle(GetVendedorByIdQuery $query): VendedorDTO
    {
        return $this->repository->findById($query->id);
    }
}
