<?php

namespace App\Domain\Carreras\Contracts;

use App\Application\Carreras\DTOs\CarreraDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface CarreraRepositoryInterface
{
    
    public function paginate(PaginationDTO $pagination, bool $conInactivos): array;

    public function findById(int $id): CarreraDTO;

    public function create(array $data): CarreraDTO;

    public function update(int $id, array $data): CarreraDTO;

    public function delete(int $id): void;
}
