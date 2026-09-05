<?php

namespace App\Application\HitosInstitucionales\QueryHandlers;

use App\Application\HitosInstitucionales\DTOs\HitoInstitucionalDTO;
use App\Application\HitosInstitucionales\Queries\GetHitoInstitucionalByIdQuery;
use App\Domain\HitosInstitucionales\Contracts\HitoInstitucionalRepositoryInterface;

class GetHitoInstitucionalByIdQueryHandler
{
    public function __construct(private readonly HitoInstitucionalRepositoryInterface $repository) {}

    public function handle(GetHitoInstitucionalByIdQuery $q): HitoInstitucionalDTO
    {
        return $this->repository->findById($q->id);
    }
}
