<?php

namespace App\Application\Carreras\QueryHandlers;

use App\Application\Carreras\Queries\GetCarrerasQuery;
use App\Domain\Carreras\Contracts\CarreraRepositoryInterface;

class GetCarrerasQueryHandler
{
    public function __construct(
        private readonly CarreraRepositoryInterface $repository
    ) {}

    public function handle(GetCarrerasQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->conInactivos);
    }
}
