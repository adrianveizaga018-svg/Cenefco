<?php

namespace App\Domain\Areas\Contracts;

use App\Application\Areas\DTOs\AreaDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface AreaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination): array;

    public function findById(int $id): AreaDTO;

    public function findBySlug(string $slug): AreaDTO;

    public function listActivas(): array;

    public function listActivasConTotalCursos(): array;

    public function findBySlugConCursos(string $slug): array;

    public function create(array $data): AreaDTO;

    public function update(int $id, array $data): AreaDTO;

    public function delete(int $id): void;
}
