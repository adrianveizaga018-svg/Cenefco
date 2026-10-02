<?php

namespace App\Domain\Trivia\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface TriviaCanjeRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, array $filters = []): array;

    public function misCanjes(int $usuarioId): array;

    public function findById(int $id): mixed;

    public function findByIdConLock(int $id): mixed;

    public function create(array $data): mixed;

    public function marcarEntregado(int $id, array $data): mixed;

    public function cancelar(int $id, array $data): mixed;

    public function puntosGastadosUsuario(int $usuarioId): int;

    public function existeCodigo(string $codigo): bool;
}
