<?php

namespace App\Domain\UsuariosAcademicos\Contracts;

use App\Application\UsuariosAcademicos\DTOs\UsuarioAcademicoDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface UsuarioAcademicoRepositoryInterface
{
    
    public function paginate(PaginationDTO $pagination, bool $conInactivos, ?string $tipoestudiante, ?int $idNiv): array;

    public function findById(int $id): UsuarioAcademicoDTO;

    public function create(array $data): UsuarioAcademicoDTO;

    public function update(int $id, array $data): UsuarioAcademicoDTO;

    public function delete(int $id): void;
}
