<?php

namespace App\Application\WebMenus\QueryHandlers;

use App\Application\WebMenus\Queries\GetWebMenusQuery;
use App\Domain\WebMenus\Contracts\WebMenuRepositoryInterface;

class GetWebMenusQueryHandler
{
    public function __construct(
        private readonly WebMenuRepositoryInterface $repository,
    ) {}

    public function handle(GetWebMenusQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->query);
    }
}
