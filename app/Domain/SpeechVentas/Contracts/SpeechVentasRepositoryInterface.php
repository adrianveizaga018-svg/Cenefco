<?php

namespace App\Domain\SpeechVentas\Contracts;

interface SpeechVentasRepositoryInterface
{
    public function paginate(int $pageIndex, int $pageSize, ?string $query, ?string $categoria, ?bool $activo): array;
    public function findById(int $id): object;
    public function create(array $data): object;
    public function update(int $id, array $data): void;
    public function delete(int $id): void;
    public function toggleActivo(int $id): object;
    public function getCategorias(): array;
    public function publicIndex(?string $q, ?string $categoria): array;
}
