<?php

namespace App\Application\Etiquetas\QueryHandlers;

use App\Application\Etiquetas\Queries\GetEtiquetasQuery;
use App\Domain\Etiquetas\Contracts\EtiquetaRepositoryInterface;

class GetEtiquetasQueryHandler
{
    public function __construct(private readonly EtiquetaRepositoryInterface $repository) {}

    public function handle(GetEtiquetasQuery $query): array
    {
        return $this->repository->paginate($query->pagination);
    }
}
