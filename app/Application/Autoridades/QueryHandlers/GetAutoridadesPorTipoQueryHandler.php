<?php

namespace App\Application\Autoridades\QueryHandlers;

use App\Application\Autoridades\Queries\GetAutoridadesPorTipoQuery;
use App\Domain\Autoridades\Contracts\AutoridadRepositoryInterface;

class GetAutoridadesPorTipoQueryHandler
{
    public function __construct(private readonly AutoridadRepositoryInterface $repository) {}

    public function handle(GetAutoridadesPorTipoQuery $query): array
    {
        return $this->repository->porTipo($query->tipo, $query->soloActivos, $query->soloPublicadas);
    }
}
