<?php

namespace App\Application\SeccionesBloque\QueryHandlers;

use App\Application\SeccionesBloque\DTOs\SeccionBloqueDTO;
use App\Application\SeccionesBloque\Queries\GetSeccionBloqueByIdQuery;
use App\Domain\SeccionesBloque\Contracts\SeccionBloqueRepositoryInterface;

class GetSeccionBloqueByIdQueryHandler
{
    public function __construct(
        private readonly SeccionBloqueRepositoryInterface $repository,
    ) {}

    public function handle(GetSeccionBloqueByIdQuery $query): SeccionBloqueDTO
    {
        return SeccionBloqueDTO::fromRow($this->repository->findById($query->idSeccionbloque));
    }
}
