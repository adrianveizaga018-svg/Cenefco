<?php

namespace App\Domain\CertConfigProgramas\Contracts;

interface CertConfigItemRepositoryInterface
{
    public function findById(int $id): mixed;
    public function findByConfigId(int $configId): array;
    public function create(array $data): mixed;
    public function update(int $id, array $data): mixed;
    public function delete(int $id): bool;
    public function existeGratuitoEnConfig(int $configId, ?int $exceptoId = null): bool;
}
