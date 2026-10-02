<?php

namespace App\Domain\Resenas\Contracts;

use App\Application\Resenas\DTOs\ResenaDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface ResenaRepositoryInterface
{
    
    public function paginate(PaginationDTO $pagination, array $filters = []): array;

    public function findById(int $id): ResenaDTO;

    public function create(array $data): ResenaDTO;

    public function update(int $id, array $data): ResenaDTO;

    public function delete(int $id): void;

    public function estudiantesPrograma(int $programaId): array;
}
