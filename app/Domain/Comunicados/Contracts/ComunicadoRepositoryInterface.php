<?php

namespace App\Domain\Comunicados\Contracts;

use App\Application\Comunicados\DTOs\ComunicadoDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface ComunicadoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloActivos = false): array;

    public function findById(int $id): ComunicadoDTO;

    public function findBySlug(string $slug): ComunicadoDTO;

    public function create(array $data): ComunicadoDTO;

    public function update(int $id, array $data): ComunicadoDTO;

    public function delete(int|array $ids): bool;
}
