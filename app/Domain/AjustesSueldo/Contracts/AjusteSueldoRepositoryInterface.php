<?php

namespace App\Domain\AjustesSueldo\Contracts;

interface AjusteSueldoRepositoryInterface
{
    public function paginate(array $filters): array;

    public function findById(int $id): mixed;

    public function pendientesDelPeriodo(int $empleadoId, int $anio, int $mes): array;

    public function create(array $data): mixed;

    public function delete(int $id): bool;

    public function marcarAplicados(array $ids, int $planillaDetalleId): void;

    public function desaplicarPorPlanillaDetalle(array $planillaDetalleIds): void;
}
