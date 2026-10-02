<?php

namespace App\Domain\Catalogos\Contracts;

use App\Application\Catalogos\DTOs\CatalogoItemDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface CatalogoRepositoryInterface
{
    
    public function paginate(string $table, string $pk, string $labelField, PaginationDTO $pagination, bool $conInactivos): array;

    public function findById(string $table, string $pk, int $id): CatalogoItemDTO;

    public function create(string $table, string $pk, string $labelField, array $data): CatalogoItemDTO;

    public function update(string $table, string $pk, int $id, array $data): CatalogoItemDTO;

    public function delete(string $table, string $pk, int $id): void;
}
