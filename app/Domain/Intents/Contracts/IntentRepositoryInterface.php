<?php

namespace App\Domain\Intents\Contracts;

interface IntentRepositoryInterface
{
    public function index(?string $query, ?string $dominio): array;
    public function findById(int $id): object;
    public function create(array $data): object;
    public function update(int $id, array $data): void;
    public function delete(int $id): void;
    public function toggleActivo(int $id): bool;
}
