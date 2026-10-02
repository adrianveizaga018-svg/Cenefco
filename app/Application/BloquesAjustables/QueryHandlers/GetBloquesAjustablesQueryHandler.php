<?php

namespace App\Application\BloquesAjustables\QueryHandlers;

use App\Application\BloquesAjustables\Queries\GetBloquesAjustablesQuery;
use App\Domain\BloquesAjustables\Contracts\BloqueAjustableRepositoryInterface;

class GetBloquesAjustablesQueryHandler
{
    public function __construct(
        private readonly BloqueAjustableRepositoryInterface $repository,
    ) {}

    public function handle(GetBloquesAjustablesQuery $query): array
    {
        return $this->repository->paginate(
            $query->pagination,
            $query->idPagina,
            $query->idBloqueplantilla,
            $query->conInactivos,
        );
    }
}
