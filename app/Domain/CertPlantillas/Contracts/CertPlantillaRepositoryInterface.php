<?php

namespace App\Domain\CertPlantillas\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface CertPlantillaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?string $tipo, bool $soloActivos): array;
    public function findById(int $id): mixed;
    public function create(array $data): mixed;
    public function update(int $id, array $data): mixed;
    public function delete(int $id): bool;
}
