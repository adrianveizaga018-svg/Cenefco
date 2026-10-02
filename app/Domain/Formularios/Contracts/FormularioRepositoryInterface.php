<?php

namespace App\Domain\Formularios\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface FormularioRepositoryInterface
{
    public function paginate(PaginationDTO $pagination): array;
    public function findById(int $id): mixed;
    public function findBySlug(string $slug): mixed;
    public function findAllActivos(): array;
    public function create(array $data): mixed;
    public function update(int $id, array $data): mixed;
    public function delete(int $id): bool;
}
