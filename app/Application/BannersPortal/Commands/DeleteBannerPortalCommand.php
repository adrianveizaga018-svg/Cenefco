<?php

namespace App\Application\BannersPortal\Commands;

final readonly class DeleteBannerPortalCommand
{
    public function __construct(public int $id) {}
}
