<?php

namespace App\Domain\CertConfigProgramas\Contracts;

interface CertSolicitudRepositoryInterface
{
    public function paginate(int $pageSize, int $page, ?string $estado, ?int $programaId): array;
    public function findById(int $id): mixed;
    public function createMany(array $rows): array;
    public function update(int $id, array $data): mixed;
    public function countPendientes(): int;
}
