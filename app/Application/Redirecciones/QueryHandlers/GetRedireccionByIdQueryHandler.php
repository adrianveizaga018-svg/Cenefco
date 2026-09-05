<?php

namespace App\Application\Redirecciones\QueryHandlers;

use App\Application\Redirecciones\DTOs\RedireccionDTO;
use App\Application\Redirecciones\Queries\GetRedireccionByIdQuery;
use App\Domain\Redirecciones\Contracts\RedireccionRepositoryInterface;

class GetRedireccionByIdQueryHandler
{
    public function __construct(private readonly RedireccionRepositoryInterface $repository) {}

    public function handle(GetRedireccionByIdQuery $query): RedireccionDTO
    {
        return $this->repository->findById($query->id);
    }
}
