<?php

namespace App\Domain\WebMenus\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface WebMenuRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?string $query): array;
    public function findById(int $id): mixed;
    public function findByNombre(string $nombre): mixed;
    public function create(array $data): mixed;
    public function update(int $id, array $data): void;
    public function delete(int $id): void;
}
