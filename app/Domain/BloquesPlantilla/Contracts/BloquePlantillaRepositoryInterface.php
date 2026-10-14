<?php

namespace App\Domain\BloquesPlantilla\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface BloquePlantillaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $conInactivos): array;
    public function findById(int $id): mixed;
    public function create(array $data): mixed;
    public function update(int $id, array $data): void;
    /** Baja lógica (estado = 0). */
    public function delete(int $id): void;
}
