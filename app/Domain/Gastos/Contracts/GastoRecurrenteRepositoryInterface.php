<?php

namespace App\Domain\Gastos\Contracts;

interface GastoRecurrenteRepositoryInterface
{
    public function findAllActivos(): array;
    public function findById(int $id): mixed;
    public function create(array $data): mixed;
    public function update(int $id, array $data): mixed;
    public function delete(int $id): bool;
    public function marcarConfirmado(int $id, string $fecha): mixed;
    public function pendientesDelMes(int $anio, int $mes): array;
}
