<?php

namespace App\Domain\Trivia\Contracts;

use App\Application\Trivia\DTOs\TriviaNivelDTO;

interface TriviaNivelRepositoryInterface
{
    public function findByCategoria(int $categoriaId, bool $soloActivos = false): array;

    public function findById(int $id): TriviaNivelDTO;

    public function create(array $data): TriviaNivelDTO;

    public function update(int $id, array $data): TriviaNivelDTO;

    public function delete(int|array $ids): bool;
}
