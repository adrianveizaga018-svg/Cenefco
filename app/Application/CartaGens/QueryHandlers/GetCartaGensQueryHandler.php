<?php

namespace App\Application\CartaGens\QueryHandlers;

use App\Application\CartaGens\Queries\GetCartaGensQuery;
use App\Domain\CartaGens\Contracts\CartaGenRepositoryInterface;

class GetCartaGensQueryHandler
{
    public function __construct(private readonly CartaGenRepositoryInterface $repository) {}

    public function handle(GetCartaGensQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->idUs, $query->idCartamod, $query->conInactivos);
    }
}
