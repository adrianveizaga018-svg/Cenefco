<?php

namespace App\Domain\CategoriasPrograma\Contracts;

use App\Application\CategoriasPrograma\DTOs\CategoriaProgramaDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface CategoriaProgramaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloActivos = false): array;

    public function findById(int $id): CategoriaProgramaDTO;

    public function findBySlug(string $slug): CategoriaProgramaDTO;

    public function create(array $data): CategoriaProgramaDTO;

    public function update(int $id, array $data): CategoriaProgramaDTO;

    public function delete(int $id): bool;

    public function tiposLegacy(): array;

    public function indexComoTipos(): array;
}
