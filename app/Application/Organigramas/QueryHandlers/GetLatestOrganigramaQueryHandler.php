<?php

namespace App\Application\Organigramas\QueryHandlers;

use App\Application\Organigramas\Queries\GetLatestOrganigramaQuery;
use App\Domain\Organigramas\Contracts\OrganigramaRepositoryInterface;

class GetLatestOrganigramaQueryHandler
{
    public function __construct(private readonly OrganigramaRepositoryInterface $repository) {}

    public function handle(GetLatestOrganigramaQuery $query): ?array
    {
        return $this->repository->findLatest();
    }
}
