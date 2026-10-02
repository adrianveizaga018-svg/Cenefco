<?php

namespace App\Application\Profesiones\QueryHandlers;

use App\Application\Profesiones\Queries\GetProfesionesQuery;
use App\Domain\Profesiones\Contracts\ProfesionRepositoryInterface;

class GetProfesionesQueryHandler
{
    public function __construct(private readonly ProfesionRepositoryInterface $repository) {}

    public function handle(GetProfesionesQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->query, $query->soloActivos);
    }
}
