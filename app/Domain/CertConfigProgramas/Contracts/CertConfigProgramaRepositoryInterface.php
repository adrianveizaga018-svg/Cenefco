<?php

namespace App\Domain\CertConfigProgramas\Contracts;

interface CertConfigProgramaRepositoryInterface
{
    public function all(): array;
    public function findById(int $id): mixed;
    public function findByProgramaId(int $programaId): mixed;
    public function create(array $data): mixed;
    public function update(int $id, array $data): mixed;
    public function delete(int $id): bool;
}
