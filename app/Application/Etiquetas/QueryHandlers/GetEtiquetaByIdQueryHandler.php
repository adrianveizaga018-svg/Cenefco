<?php

namespace App\Application\Etiquetas\QueryHandlers;

use App\Application\Etiquetas\DTOs\EtiquetaDTO;
use App\Application\Etiquetas\Queries\GetEtiquetaByIdQuery;
use App\Domain\Etiquetas\Contracts\EtiquetaRepositoryInterface;

class GetEtiquetaByIdQueryHandler
{
    public function __construct(private readonly EtiquetaRepositoryInterface $repository) {}

    public function handle(GetEtiquetaByIdQuery $query): EtiquetaDTO
    {
        return $this->repository->findById($query->id);
    }
}
