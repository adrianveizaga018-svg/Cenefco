<?php

namespace App\Domain\RevistasCientificas\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface RevistaCientificaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?string $query, bool $conInactivos): array;
    public function findById(int $id): mixed;
    public function create(array $data): mixed;
    public function update(int $id, array $data): void;
    public function softDelete(int $id): void;
}
