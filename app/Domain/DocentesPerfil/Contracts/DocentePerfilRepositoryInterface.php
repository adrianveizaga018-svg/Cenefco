<?php

namespace App\Domain\DocentesPerfil\Contracts;

use App\Application\DocentesPerfil\DTOs\DocentePerfilDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface DocentePerfilRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloVisibles = false): array;

    public function findById(int $id): DocentePerfilDTO;

    public function findBySlug(string $slug): DocentePerfilDTO;

    public function create(array $data): DocentePerfilDTO;

    public function update(int $id, array $data): DocentePerfilDTO;

    public function delete(int|array $ids): bool;

    public function reporteMaterias(?string $gestion, ?string $periodo): array;
}
