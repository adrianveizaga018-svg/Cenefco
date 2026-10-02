<?php

namespace App\Application\Vendedores\QueryHandlers;

use App\Application\Vendedores\Queries\GetVendedoresQuery;
use App\Domain\Vendedores\Contracts\VendedorRepositoryInterface;

class GetVendedoresQueryHandler
{
    public function __construct(private readonly VendedorRepositoryInterface $repository) {}

    public function handle(GetVendedoresQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->activo);
    }
}
