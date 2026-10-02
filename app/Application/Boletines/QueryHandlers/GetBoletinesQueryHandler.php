<?php

namespace App\Application\Boletines\QueryHandlers;

use App\Application\Boletines\Queries\GetBoletinesQuery;
use App\Domain\Boletines\Contracts\BoletinRepositoryInterface;

class GetBoletinesQueryHandler
{
    public function __construct(private readonly BoletinRepositoryInterface $repository) {}

    public function handle(GetBoletinesQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->query, $query->conInactivos);
    }
}
