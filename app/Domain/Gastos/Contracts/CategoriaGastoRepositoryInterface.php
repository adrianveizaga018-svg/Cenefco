<?php

namespace App\Domain\Gastos\Contracts;

interface CategoriaGastoRepositoryInterface
{
    public function findAllActivas(): array;
    public function findById(int $id): mixed;
    public function findIdByNombre(string $nombre): ?int;
    public function create(array $data): mixed;
    public function update(int $id, array $data): mixed;
    public function delete(int $id): bool;
}
