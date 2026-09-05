<?php

namespace App\Domain\Vendedores\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface VendedorRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?bool $activo = null): array;
    public function findById(int $id): mixed;
    public function create(array $data): mixed;
    public function update(int $id, array $data): mixed;
    public function delete(int|array $ids): bool;
}
