<?php

namespace App\Domain\Certificados\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface CertificadoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?int $usuarioId, ?int $imparteId, ?string $estado): array;
    public function findById(int $id): mixed;
    public function findByCodigo(string $codigo): mixed;
    public function create(array $data): mixed;
    public function update(int $id, array $data): mixed;
    public function delete(int $id): bool;
}
