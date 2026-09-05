<?php

namespace App\Application\Asesores\QueryHandlers;

use App\Application\Asesores\Queries\GetAsesoresQuery;
use App\Domain\Asesores\Contracts\AsesorRepositoryInterface;

class GetAsesoresQueryHandler
{
    public function __construct(private readonly AsesorRepositoryInterface $repository) {}

    public function handle(GetAsesoresQuery $query): array
    {
        return $this->repository->paginate(
            $query->pageIndex,
            $query->pageSize,
            $query->query,
            $query->activo,
        );
    }
}
