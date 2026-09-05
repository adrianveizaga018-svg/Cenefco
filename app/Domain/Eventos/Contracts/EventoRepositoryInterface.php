<?php

namespace App\Domain\Eventos\Contracts;

use App\Application\Eventos\DTOs\EventoDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface EventoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloActivos = false): array;

    public function findById(int $id): EventoDTO;

    public function findBySlug(string $slug): EventoDTO;

    public function create(array $data): EventoDTO;

    public function update(int $id, array $data): EventoDTO;

    public function delete(int|array $ids): bool;
}
