<?php

namespace App\Application\Autoridades\QueryHandlers;

use App\Application\Autoridades\Queries\GetAutoridadesQuery;
use App\Domain\Autoridades\Contracts\AutoridadRepositoryInterface;

class GetAutoridadesQueryHandler
{
    public function __construct(private readonly AutoridadRepositoryInterface $repository) {}

    public function handle(GetAutoridadesQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->soloActivos, $query->soloPublicadas);
    }
}
