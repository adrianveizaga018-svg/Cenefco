<?php

namespace App\Application\Areas\QueryHandlers;

use App\Application\Areas\DTOs\AreaDTO;
use App\Application\Areas\Queries\GetAreaBySlugQuery;
use App\Domain\Areas\Contracts\AreaRepositoryInterface;

class GetAreaBySlugQueryHandler
{
    public function __construct(
        private readonly AreaRepositoryInterface $repository,
    ) {}

    public function handle(GetAreaBySlugQuery $query): AreaDTO
    {
        return $this->repository->findBySlug($query->slug);
    }
}
