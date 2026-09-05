<?php

namespace App\Domain\Convenios\Contracts;

use App\Application\Convenios\DTOs\ConvenioDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface ConvenioRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloActivos = false): array;

    public function all(): array;

    public function findById(int $id): ConvenioDTO;

    public function create(array $data): ConvenioDTO;

    public function update(int $id, array $data): ConvenioDTO;

    public function delete(int $id): bool;
}
