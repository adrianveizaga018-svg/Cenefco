<?php

namespace App\Domain\CompromisosCobro\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Support\Collection;

interface CompromisoCobroRepositoryInterface
{
    public function create(array $data): mixed;

    public function update(int $id, array $data): mixed;

    public function findById(int $id): mixed;

    public function findAbiertoPorInscripcion(int $idIns): mixed;

    public function existeAbiertoPara(int $idIns): bool;

    public function existeAbiertoParaConLock(int $idIns): bool;

    public function registrarLog(int $compromisoCobroId, array $data): void;

    public function logsDe(int $compromisoCobroId): Collection;

    public function paginate(PaginationDTO $pagination, ?string $estado, ?array $idImpPermitidos): array;

    public function resumen(?array $idImpPermitidos): array;

    public function pendientesConFechaHoySinNotificar(): Collection;

    public function pendientesVencidosSinNotificar(): Collection;

    public function marcarCumplidosDe(int $idIns, int $registradoPor): void;
}
