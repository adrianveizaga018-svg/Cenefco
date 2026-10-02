<?php

namespace App\Application\Areas\QueryHandlers;

use App\Application\Areas\Queries\GetAreaConCursosQuery;
use App\Domain\Areas\Contracts\AreaRepositoryInterface;

class GetAreaConCursosQueryHandler
{
    public function __construct(
        private readonly AreaRepositoryInterface $repository,
    ) {}

    public function handle(GetAreaConCursosQuery $query): array
    {
        return $this->repository->findBySlugConCursos($query->slug);
    }
}
