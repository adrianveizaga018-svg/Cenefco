<?php

namespace App\Application\Ayudas\QueryHandlers;

use App\Application\Ayudas\Queries\GetAyudasQuery;
use App\Domain\Ayudas\Contracts\AyudaRepositoryInterface;

class GetAyudasQueryHandler
{
    public function __construct(private readonly AyudaRepositoryInterface $repository) {}

    public function handle(GetAyudasQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->idUs, $query->gestion, $query->conInactivos);
    }
}
