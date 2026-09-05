<?php

namespace App\Application\BannersPortal\Handlers;

use App\Application\BannersPortal\Commands\DeleteBannerPortalCommand;
use App\Domain\BannersPortal\Contracts\BannerPortalRepositoryInterface;

class DeleteBannerPortalHandler
{
    public function __construct(private readonly BannerPortalRepositoryInterface $repository) {}

    public function handle(DeleteBannerPortalCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
