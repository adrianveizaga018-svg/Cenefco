<?php

namespace App\Domain\Historiales\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface HistorialRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?int $idUs, ?int $idTiporeferencia, ?int $idTipohistorial, bool $conInactivos): array;
    public function findById(int $id): mixed;
    public function create(array $data): mixed;
    public function update(int $id, array $data): void;
    public function softDelete(int $id): void;
}
