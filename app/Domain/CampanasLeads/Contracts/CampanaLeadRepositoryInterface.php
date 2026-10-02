<?php

namespace App\Domain\CampanasLeads\Contracts;

use App\Application\CampanasLeads\DTOs\CampanaLeadDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface CampanaLeadRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?string $estado = null): array;

    public function findById(int $id): CampanaLeadDTO;

    public function create(array $data): CampanaLeadDTO;

    public function update(int $id, array $data): CampanaLeadDTO;

    public function delete(int $id): bool;
}
