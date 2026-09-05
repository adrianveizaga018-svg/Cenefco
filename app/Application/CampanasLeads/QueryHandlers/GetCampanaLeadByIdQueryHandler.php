<?php

namespace App\Application\CampanasLeads\QueryHandlers;

use App\Application\CampanasLeads\DTOs\CampanaLeadDTO;
use App\Application\CampanasLeads\Queries\GetCampanaLeadByIdQuery;
use App\Domain\CampanasLeads\Contracts\CampanaLeadRepositoryInterface;

class GetCampanaLeadByIdQueryHandler
{
    public function __construct(private readonly CampanaLeadRepositoryInterface $repository) {}

    public function handle(GetCampanaLeadByIdQuery $query): CampanaLeadDTO
    {
        return $this->repository->findById($query->id);
    }
}
