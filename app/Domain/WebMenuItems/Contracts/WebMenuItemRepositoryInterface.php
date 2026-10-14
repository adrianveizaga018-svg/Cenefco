<?php

namespace App\Domain\WebMenuItems\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface WebMenuItemRepositoryInterface
{
    public function findById(int $id): mixed;
    public function byMenu(int $menuId): array;
    public function create(array $data): mixed;
    public function update(int $id, array $data): void;
    public function delete(int $id): void;
}
