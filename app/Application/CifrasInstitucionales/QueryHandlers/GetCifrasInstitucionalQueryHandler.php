<?php

namespace App\Application\CifrasInstitucionales\QueryHandlers;

use App\Application\CifrasInstitucionales\Queries\GetCifrasInstitucionalQuery;
use App\Domain\CifrasInstitucionales\Contracts\CifraInstitucionalRepositoryInterface;

class GetCifrasInstitucionalQueryHandler
{
    public function __construct(private readonly CifraInstitucionalRepositoryInterface $repository) {}

    public function handle(GetCifrasInstitucionalQuery $q): array
    {
        if ($q->publicoSoloActivas) {
            return $this->repository->getAllActivas();
        }

        return $this->repository->paginate($q->pagination);
    }
}
