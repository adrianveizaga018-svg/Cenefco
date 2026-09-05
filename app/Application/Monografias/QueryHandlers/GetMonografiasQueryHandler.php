<?php

namespace App\Application\Monografias\QueryHandlers;

use App\Application\Monografias\Queries\GetMonografiasQuery;
use App\Domain\Monografias\Contracts\MonografiaRepositoryInterface;

class GetMonografiasQueryHandler
{
    public function __construct(private readonly MonografiaRepositoryInterface $repository) {}

    public function handle(GetMonografiasQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->query, $query->conInactivos);
    }
}
