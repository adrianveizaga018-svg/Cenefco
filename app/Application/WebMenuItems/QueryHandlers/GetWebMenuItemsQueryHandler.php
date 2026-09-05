<?php

namespace App\Application\WebMenuItems\QueryHandlers;

use App\Application\WebMenuItems\Queries\GetWebMenuItemsQuery;
use App\Domain\WebMenuItems\Contracts\WebMenuItemRepositoryInterface;

class GetWebMenuItemsQueryHandler
{
    public function __construct(
        private readonly WebMenuItemRepositoryInterface $repository,
    ) {}

    public function handle(GetWebMenuItemsQuery $query): array
    {
        return $this->repository->byMenu($query->menuId);
    }
}
