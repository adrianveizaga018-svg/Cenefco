<?php

namespace App\Domain\TiposPostgrado\Contracts;

interface TipoPostgradoRepositoryInterface
{
    public function paginate(int $pageIndex, int $pageSize, ?int $idPlan, ?int $idTipopago, bool $conInactivos): array;
    public function findById(int $id): object;
    public function create(array $data): object;
    public function update(int $id, array $data): void;
    public function delete(int $id): void;
}
