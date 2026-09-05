<?php

namespace App\Application\BannersPortal\QueryHandlers;

use App\Application\BannersPortal\DTOs\BannerPortalDTO;
use App\Application\BannersPortal\Queries\GetBannerPortalBySlugQuery;
use App\Domain\BannersPortal\Contracts\BannerPortalRepositoryInterface;

class GetBannerPortalBySlugQueryHandler
{
    public function __construct(private readonly BannerPortalRepositoryInterface $repository) {}

    public function handle(GetBannerPortalBySlugQuery $query): BannerPortalDTO
    {
        return $this->repository->findBySlug($query->slug);
    }
}
