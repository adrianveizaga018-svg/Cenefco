<?php

namespace App\Domain\Pagos\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface PagoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, array $filters = []): array;
    public function findById(int $id): mixed;
    public function existePagoActivo(int $idUs, int $idFechapago): bool;
    public function existePorBoleta(string $nroBoleta): bool;

    public function existePagoActivoConLock(int $idUs, int $idFechapago): bool;
    public function existePorBoletaConLock(string $nroBoleta): bool;

    public function create(array $data): mixed;
    public function update(int $id, array $data): mixed;
    public function anular(int $id, int $idUsReg): mixed;
    public function auditarCambio(object $pago, string $tipoLog, int $idUsReg): void;

    public function registrarLogPagoOnlineFallido(string $referencia, int $idUs, float $monto, string $detalle): void;
}
