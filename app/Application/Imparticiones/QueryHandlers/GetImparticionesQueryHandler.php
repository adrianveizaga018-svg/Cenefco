<?php

namespace App\Application\Imparticiones\QueryHandlers;

use App\Application\Imparticiones\Queries\GetImparticionesQuery;
use App\Domain\Imparticiones\Contracts\ImparteRepositoryInterface;

class GetImparticionesQueryHandler
{
    public function __construct(
        private readonly ImparteRepositoryInterface $repository
    ) {}

    public function handle(GetImparticionesQuery $query): array
    {
        return $this->repository->paginate(
            $query->pagination,
            $query->conInactivos,
            $query->periodo,
            $query->gestion,
            $query->idMat,
            $query->idUs,
        );
    }
}
