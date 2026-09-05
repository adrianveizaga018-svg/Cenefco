<?php

namespace App\Domain\Pagos\Contracts;

interface DevolucionRepositoryInterface
{
    public function findByInscripcion(int $idIns): array;
    public function create(array $data): mixed;
    public function update(int $id, array $data): mixed;

    public function findByIdConLock(int $id): mixed;
}
