<?php

namespace App\Application\ListaAprobados\QueryHandlers;

use App\Application\ListaAprobados\DTOs\ListaAprobadosDTO;
use App\Application\ListaAprobados\Queries\GetListaAprobadoByIdQuery;
use App\Domain\ListaAprobados\Contracts\ListaAprobadosRepositoryInterface;

class GetListaAprobadoByIdQueryHandler
{
    public function __construct(
        private readonly ListaAprobadosRepositoryInterface $repository
    ) {}

    public function handle(GetListaAprobadoByIdQuery $query): ListaAprobadosDTO
    {
        return $this->repository->findById($query->id);
    }
}
