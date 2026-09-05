<?php

namespace App\Application\WebMenus\QueryHandlers;

use App\Application\WebMenus\DTOs\WebMenuDTO;
use App\Application\WebMenus\Queries\GetWebMenuByIdQuery;
use App\Domain\WebMenus\Contracts\WebMenuRepositoryInterface;

class GetWebMenuByIdQueryHandler
{
    public function __construct(
        private readonly WebMenuRepositoryInterface $repository,
    ) {}

    public function handle(GetWebMenuByIdQuery $query): WebMenuDTO
    {
        return WebMenuDTO::fromRow($this->repository->findById($query->id));
    }
}
