<?php

namespace App\Domain\BloquesAjustables\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface BloqueAjustableRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?int $idPagina, ?int $idBloqueplantilla, bool $conInactivos): array;
    public function findById(int $id): mixed;
    public function create(array $data): mixed;
    public function update(int $id, array $data): void;
    /** Baja lógica (estado = 0). */
    public function delete(int $id): void;
}
