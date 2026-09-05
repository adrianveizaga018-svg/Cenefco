<?php

namespace App\Application\Cartas\QueryHandlers;

use App\Application\Cartas\Queries\GetCartasQuery;
use App\Domain\Cartas\Contracts\CartaRepositoryInterface;

class GetCartasQueryHandler
{
    public function __construct(private readonly CartaRepositoryInterface $repository) {}

    public function handle(GetCartasQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->idUs, $query->idPlan, $query->conInactivos);
    }
}
