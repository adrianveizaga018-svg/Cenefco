<?php

namespace App\Domain\Expedidos\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface ExpedidoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?string $query): array;
    public function findById(int $id): mixed;
    public function create(array $data): mixed;
    public function update(int $id, array $data): void;
    public function delete(int $id): void;
}
