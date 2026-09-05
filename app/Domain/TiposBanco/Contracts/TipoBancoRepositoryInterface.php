<?php

namespace App\Domain\TiposBanco\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface TipoBancoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination): array;
    public function findAllActivos(): array;
    public function findById(int $id): mixed;
    public function create(array $data): mixed;
    public function update(int $id, array $data): mixed;
    public function delete(int $id): bool;
}
