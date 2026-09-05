<?php

namespace App\Application\CampanasLeads\QueryHandlers;

use App\Application\CampanasLeads\DTOs\LeadDTO;
use App\Application\CampanasLeads\Queries\GetLeadsQuery;
use App\Domain\CampanasLeads\Contracts\LeadRepositoryInterface;

class GetLeadsQueryHandler
{
    public function __construct(private readonly LeadRepositoryInterface $repository) {}

    public function handle(GetLeadsQuery $query): array
    {
        return $this->repository->paginate($query->campanaLeadId, $query->pagination);
    }

    public function findById(int $campanaLeadId, int $id): LeadDTO
    {
        return $this->repository->findById($campanaLeadId, $id);
    }
}
