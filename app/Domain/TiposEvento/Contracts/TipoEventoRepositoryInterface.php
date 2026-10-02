<?php

namespace App\Domain\TiposEvento\Contracts;

use App\Application\TiposEvento\DTOs\TipoEventoDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface TipoEventoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination): array;

    public function findById(int $id): TipoEventoDTO;

    public function findBySlug(string $slug): TipoEventoDTO;

    public function create(array $data): TipoEventoDTO;

    public function update(int $id, array $data): TipoEventoDTO;

    public function delete(int|array $ids): bool;
}
