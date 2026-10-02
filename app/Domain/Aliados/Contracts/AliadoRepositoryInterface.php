<?php

namespace App\Domain\Aliados\Contracts;

use App\Application\Aliados\DTOs\AliadoDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface AliadoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloActivos = false): array;

    public function findById(int $id): AliadoDTO;

    public function create(array $data): AliadoDTO;

    public function update(int $id, array $data): AliadoDTO;

    public function delete(int|array $ids): bool;
}
