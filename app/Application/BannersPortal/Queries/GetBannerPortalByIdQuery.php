<?php

namespace App\Application\BannersPortal\Queries;

final readonly class GetBannerPortalByIdQuery
{
    public function __construct(public int $id) {}
}
