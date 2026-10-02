<?php

namespace App\Application\BannersPortal\Queries;

final readonly class GetBannerPortalBySlugQuery
{
    public function __construct(public string $slug) {}
}
