<?php

namespace App\Application\Etiquetas\QueryHandlers;

use App\Application\Etiquetas\DTOs\EtiquetaDTO;
use App\Application\Etiquetas\Queries\GetEtiquetaBySlugQuery;
use App\Domain\Etiquetas\Contracts\EtiquetaRepositoryInterface;

class GetEtiquetaBySlugQueryHandler
{
    public function __construct(private readonly EtiquetaRepositoryInterface $repository) {}

    public function handle(GetEtiquetaBySlugQuery $query): EtiquetaDTO
    {
        return $this->repository->findBySlug($query->slug);
    }
}
