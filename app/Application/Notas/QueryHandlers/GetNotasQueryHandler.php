<?php

namespace App\Application\Notas\QueryHandlers;

use App\Application\Notas\Queries\GetNotasQuery;
use App\Domain\Notas\Contracts\NotaRepositoryInterface;

class GetNotasQueryHandler
{
    public function __construct(
        private readonly NotaRepositoryInterface $repository
    ) {}

    public function handle(GetNotasQuery $query): array
    {
        return $this->repository->paginate(
            $query->pagination,
            $query->conInactivos,
            $query->idUs,
            $query->idImp,
            $query->idMat,
            $query->periodo,
            $query->gestion,
        );
    }
}
