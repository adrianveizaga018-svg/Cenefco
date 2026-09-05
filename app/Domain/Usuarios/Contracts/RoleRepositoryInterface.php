<?php

namespace App\Domain\Usuarios\Contracts;

use App\Application\Roles\DTOs\RoleDTO;

interface RoleRepositoryInterface
{
    
    public function all(): array;

    public function findById(int $id): RoleDTO;

    public function create(array $data): RoleDTO;

    public function update(int $id, array $data): RoleDTO;

    public function delete(int $id): void;
}
