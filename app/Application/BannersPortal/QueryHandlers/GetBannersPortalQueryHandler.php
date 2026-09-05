<?php

namespace App\Application\BannersPortal\QueryHandlers;

use App\Application\BannersPortal\Queries\GetBannersPortalQuery;
use App\Domain\BannersPortal\Contracts\BannerPortalRepositoryInterface;

class GetBannersPortalQueryHandler
{
    public function __construct(private readonly BannerPortalRepositoryInterface $repository) {}

    public function handle(GetBannersPortalQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->soloActivos);
    }
}
