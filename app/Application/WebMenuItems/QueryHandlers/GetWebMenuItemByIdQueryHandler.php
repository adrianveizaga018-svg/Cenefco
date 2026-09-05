<?php

namespace App\Application\WebMenuItems\QueryHandlers;

use App\Application\WebMenuItems\DTOs\WebMenuItemDTO;
use App\Application\WebMenuItems\Queries\GetWebMenuItemByIdQuery;
use App\Domain\WebMenuItems\Contracts\WebMenuItemRepositoryInterface;

class GetWebMenuItemByIdQueryHandler
{
    public function __construct(
        private readonly WebMenuItemRepositoryInterface $repository,
    ) {}

    public function handle(GetWebMenuItemByIdQuery $query): WebMenuItemDTO
    {
        return WebMenuItemDTO::fromRow($this->repository->findById($query->id));
    }
}
