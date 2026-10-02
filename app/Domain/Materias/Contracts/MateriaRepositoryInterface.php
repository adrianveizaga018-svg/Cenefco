<?php

namespace App\Domain\Materias\Contracts;

use App\Application\Materias\DTOs\MateriaDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface MateriaRepositoryInterface
{
    
    public function paginate(PaginationDTO $pagination, bool $conInactivos): array;

    public function findById(int $id): MateriaDTO;

    public function create(array $data): MateriaDTO;

    public function update(int $id, array $data): MateriaDTO;

    public function delete(int $id): void;
}
