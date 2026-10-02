<?php

namespace App\Domain\CertPlantillaCampos\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface CertPlantillaCampoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?int $plantillaId, bool $soloActivos): array;
    public function findById(int $id): mixed;
    public function create(array $data): mixed;
    public function update(int $id, array $data): mixed;
    public function delete(int $id): bool;
}
