<?php

namespace App\Domain\CartaGens\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface CartaGenRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?int $idUs, ?int $idCartamod, bool $conInactivos): array;
    public function findById(int $id): mixed;
    public function create(array $data): mixed;
    public function update(int $id, array $data): void;
    public function softDelete(int $id): void;
}
