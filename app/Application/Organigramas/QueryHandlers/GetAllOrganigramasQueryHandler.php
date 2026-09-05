<?php

namespace App\Application\Organigramas\QueryHandlers;

use App\Application\Organigramas\Queries\GetAllOrganigramasQuery;
use App\Domain\Organigramas\Contracts\OrganigramaRepositoryInterface;

class GetAllOrganigramasQueryHandler
{
    public function __construct(private readonly OrganigramaRepositoryInterface $repository) {}

    public function handle(GetAllOrganigramasQuery $query): array
    {
        return $this->repository->all();
    }
}
