<?php

namespace App\Domain\UsuariosTipoPrograma\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface UsuarioTipoProgramaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?int $idUs, ?int $idTipoPrograma, bool $conInactivos): array;
    public function findById(int $id): mixed;
    public function create(array $data): mixed;
    public function update(int $id, array $data): mixed;
    public function delete(int $id): void;
}
