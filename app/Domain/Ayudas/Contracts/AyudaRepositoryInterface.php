<?php

namespace App\Domain\Ayudas\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface AyudaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?int $idUs, ?string $gestion, bool $conInactivos): array;
    public function findById(int $id): mixed;
    public function create(array $data): mixed;
    public function update(int $id, array $data): void;
    public function softDelete(int $id): void;
}
