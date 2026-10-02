<?php

namespace App\Application\Aliados\QueryHandlers;

use App\Application\Aliados\Queries\GetAliadosQuery;
use App\Domain\Aliados\Contracts\AliadoRepositoryInterface;

class GetAliadosQueryHandler
{
    public function __construct(private readonly AliadoRepositoryInterface $repository) {}

    public function handle(GetAliadosQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->soloActivos);
    }
}
