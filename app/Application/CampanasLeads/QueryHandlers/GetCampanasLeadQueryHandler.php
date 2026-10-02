<?php

namespace App\Application\CampanasLeads\QueryHandlers;

use App\Application\CampanasLeads\Queries\GetCampanasLeadQuery;
use App\Domain\CampanasLeads\Contracts\CampanaLeadRepositoryInterface;

class GetCampanasLeadQueryHandler
{
    public function __construct(private readonly CampanaLeadRepositoryInterface $repository) {}

    public function handle(GetCampanasLeadQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->estado);
    }
}
