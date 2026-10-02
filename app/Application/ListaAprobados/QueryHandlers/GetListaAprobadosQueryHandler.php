<?php

namespace App\Application\ListaAprobados\QueryHandlers;

use App\Application\ListaAprobados\Queries\GetListaAprobadosQuery;
use App\Domain\ListaAprobados\Contracts\ListaAprobadosRepositoryInterface;

class GetListaAprobadosQueryHandler
{
    public function __construct(
        private readonly ListaAprobadosRepositoryInterface $repository
    ) {}

    public function handle(GetListaAprobadosQuery $query): array
    {
        return $this->repository->paginate(
            $query->pagination,
            $query->imparteId,
            $query->usuarioId,
            $query->condicion,
            $query->estadoCertificado,
        );
    }
}
