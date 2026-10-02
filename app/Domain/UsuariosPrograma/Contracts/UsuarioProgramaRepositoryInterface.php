<?php

namespace App\Domain\UsuariosPrograma\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface UsuarioProgramaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?int $idUs, ?int $idPrograma, ?int $idTipoPrograma, bool $conInactivos): array;
    public function findById(int $id): mixed;
    public function create(array $data): mixed;
    public function update(int $id, array $data): mixed;
    public function delete(int $id): void;
}
