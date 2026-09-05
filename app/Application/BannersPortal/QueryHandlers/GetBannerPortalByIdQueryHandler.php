<?php

namespace App\Application\BannersPortal\QueryHandlers;

use App\Application\BannersPortal\DTOs\BannerPortalDTO;
use App\Application\BannersPortal\Queries\GetBannerPortalByIdQuery;
use App\Domain\BannersPortal\Contracts\BannerPortalRepositoryInterface;

class GetBannerPortalByIdQueryHandler
{
    public function __construct(private readonly BannerPortalRepositoryInterface $repository) {}

    public function handle(GetBannerPortalByIdQuery $query): BannerPortalDTO
    {
        return $this->repository->findById($query->id);
    }
}
