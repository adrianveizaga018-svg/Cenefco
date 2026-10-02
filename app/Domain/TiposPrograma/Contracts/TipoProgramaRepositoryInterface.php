<?php

namespace App\Domain\TiposPrograma\Contracts;

interface TipoProgramaRepositoryInterface
{
    public function paginate(int $pageIndex, int $pageSize, ?string $query, bool $conInactivos): array;
    public function findById(int $id): object;
    public function create(array $data): object;
    public function update(int $id, array $data): void;
    public function delete(int $id): void;
}
