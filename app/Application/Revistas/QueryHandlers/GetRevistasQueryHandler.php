<?php

namespace App\Application\Revistas\QueryHandlers;

use App\Application\Revistas\Queries\GetRevistasQuery;
use App\Domain\Revistas\Contracts\RevistaRepositoryInterface;

class GetRevistasQueryHandler
{
    public function __construct(private readonly RevistaRepositoryInterface $repository) {}

    public function handle(GetRevistasQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->query, $query->conInactivos);
    }
}
