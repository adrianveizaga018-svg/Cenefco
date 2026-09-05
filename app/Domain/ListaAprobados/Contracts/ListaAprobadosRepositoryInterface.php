<?php

namespace App\Domain\ListaAprobados\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface ListaAprobadosRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?int $imparteId, ?int $usuarioId, ?string $condicion, ?string $estadoCertificado): array;
    public function findById(int $id): mixed;
    public function existeParaImparteYUsuario(int $imparteId, int $usuarioId): bool;

    public function aprobadosPendientesDeCertificado(int $imparteId, ?array $usuarioIds = null): \Illuminate\Support\Collection;
    public function create(array $data): mixed;
    public function update(int $id, array $data): mixed;
    public function delete(int $id): bool;

    public function deleteMany(array $ids): int;
}
