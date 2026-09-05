<?php

namespace App\Domain\EfectosEspeciales\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface EfectoEspecialRepositoryInterface
{
    public function paginate(PaginationDTO $pagination): array;
    public function findById(int $id): mixed;
    public function getActivos(): array;
    public function create(array $data): mixed;
    public function update(int $id, array $data): mixed;
    public function delete(int $id): bool;
}
