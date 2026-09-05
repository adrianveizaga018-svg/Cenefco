<?php

namespace App\Domain\CampanasLeads\Contracts;

use App\Application\CampanasLeads\DTOs\LeadDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface LeadRepositoryInterface
{
    public function paginate(int $campanaLeadId, PaginationDTO $pagination): array;

    public function findById(int $campanaLeadId, int $id): LeadDTO;

    public function create(int $campanaLeadId, array $data): LeadDTO;

    public function update(int $campanaLeadId, int $id, array $data): LeadDTO;

    public function delete(int $campanaLeadId, int $id): bool;
}
