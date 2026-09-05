<?php

namespace App\Application\Redirecciones\QueryHandlers;

use App\Application\Redirecciones\Queries\GetRedireccionesQuery;
use App\Domain\Redirecciones\Contracts\RedireccionRepositoryInterface;

class GetRedireccionesQueryHandler
{
    public function __construct(private readonly RedireccionRepositoryInterface $repository) {}

    public function handle(GetRedireccionesQuery $query): array
    {
        return $this->repository->paginate($query->pagination);
    }
}
