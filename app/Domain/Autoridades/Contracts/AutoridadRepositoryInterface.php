<?php

namespace App\Domain\Autoridades\Contracts;

use App\Application\Autoridades\DTOs\AutoridadDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface AutoridadRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloActivos = false, bool $soloPublicadas = false): array;

    public function findById(int $id): AutoridadDTO;

    public function findBySlug(string $slug): AutoridadDTO;

    public function porTipo(string $tipo, bool $soloActivos = true, bool $soloPublicadas = false): array;

    public function create(array $data): AutoridadDTO;

    public function update(int $id, array $data): AutoridadDTO;

    public function delete(int|array $ids): bool;
}
