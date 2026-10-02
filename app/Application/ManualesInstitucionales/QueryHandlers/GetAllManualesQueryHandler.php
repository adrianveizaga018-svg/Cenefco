<?php

namespace App\Application\ManualesInstitucionales\QueryHandlers;

use App\Application\ManualesInstitucionales\Queries\GetAllManualesQuery;
use App\Domain\ManualesInstitucionales\Contracts\ManualInstitucionalRepositoryInterface;

class GetAllManualesQueryHandler
{
    public function __construct(private readonly ManualInstitucionalRepositoryInterface $repository) {}

    public function handle(GetAllManualesQuery $query): array
    {
        return $this->repository->all();
    }
}
