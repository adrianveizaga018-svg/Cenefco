<?php

namespace App\Domain\Trivia\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface TriviaPremioRepositoryInterface
{
    public function paginate(PaginationDTO $pagination): array;

    public function findById(int $id): mixed;

    public function activos(): array;

    public function findByIdConLock(int $id): mixed;

    public function create(array $data): mixed;

    public function update(int $id, array $data): mixed;

    public function delete(int $id): bool;

    public function decrementarStock(int $id): void;

    public function incrementarStock(int $id): void;
}
