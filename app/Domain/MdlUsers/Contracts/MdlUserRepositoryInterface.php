<?php

namespace App\Domain\MdlUsers\Contracts;

use App\Application\MdlUsers\DTOs\MdlUserDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface MdlUserRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?string $query, ?int $idUsReg, bool $conInactivos): array;
    public function findById(int $id): object;
    public function create(array $data): object;
    public function update(int $id, array $data): void;
    public function delete(int $id): void;
}
