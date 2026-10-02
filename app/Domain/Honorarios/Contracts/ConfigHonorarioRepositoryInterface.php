<?php

namespace App\Domain\Honorarios\Contracts;

interface ConfigHonorarioRepositoryInterface
{
    public function findAll(): array;
    public function findByPrograma(int $idPrograma): mixed;
    public function upsert(int $idPrograma, array $data): mixed;
    public function delete(int $idPrograma): bool;
}
