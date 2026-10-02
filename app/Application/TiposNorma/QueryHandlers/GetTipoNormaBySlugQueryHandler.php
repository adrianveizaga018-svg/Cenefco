<?php

namespace App\Application\TiposNorma\QueryHandlers;

use App\Application\TiposNorma\DTOs\TipoNormaDTO;
use App\Application\TiposNorma\Queries\GetTipoNormaBySlugQuery;
use App\Domain\TiposNorma\Contracts\TipoNormaRepositoryInterface;

class GetTipoNormaBySlugQueryHandler
{
    public function __construct(private readonly TipoNormaRepositoryInterface $repository) {}

    public function handle(GetTipoNormaBySlugQuery $query): TipoNormaDTO
    {
        return $this->repository->findBySlug($query->slug);
    }
}
