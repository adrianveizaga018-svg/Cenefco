<?php

namespace App\Application\HitosInstitucionales\QueryHandlers;

use App\Application\HitosInstitucionales\Queries\GetHitosInstitucionalQuery;
use App\Domain\HitosInstitucionales\Contracts\HitoInstitucionalRepositoryInterface;

class GetHitosInstitucionalQueryHandler
{
    public function __construct(private readonly HitoInstitucionalRepositoryInterface $repository) {}

    public function handle(GetHitosInstitucionalQuery $q): array
    {
        return $this->repository->paginate($q->pagination, $q->soloActivos);
    }
}
