<?php

namespace App\Application\CitasAsesoria\QueryHandlers;

use App\Application\CitasAsesoria\Queries\GetCitasAsesoriaQuery;
use App\Domain\CitasAsesoria\Contracts\CitaAsesoriaRepositoryInterface;

class GetCitasAsesoriaQueryHandler
{
    public function __construct(
        private readonly CitaAsesoriaRepositoryInterface $repository,
    ) {}

    public function handle(GetCitasAsesoriaQuery $query): array
    {
        return $this->repository->paginate(
            $query->pagination,
            $query->query,
            $query->estado,
        );
    }
}
