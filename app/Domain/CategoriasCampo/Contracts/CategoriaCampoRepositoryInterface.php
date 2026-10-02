<?php

namespace App\Domain\CategoriasCampo\Contracts;

use App\Application\CategoriasCampo\DTOs\CategoriaCampoDTO;

interface CategoriaCampoRepositoryInterface
{
    public function findByCategoria(int $categoriaId): array;

    public function findById(int $id): CategoriaCampoDTO;

    public function findByCategoriaAndId(int $categoriaId, int $id): CategoriaCampoDTO;

    public function create(array $data): CategoriaCampoDTO;

    public function update(int $id, array $data): CategoriaCampoDTO;

    public function delete(int $categoriaId, int $id): bool;

    public function reorder(int $categoriaId, array $items): void;

    public function existeNombreCampo(int $categoriaId, string $nombreCampo, ?int $excludeId = null): bool;
}
