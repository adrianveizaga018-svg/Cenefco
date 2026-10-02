<?php

namespace App\Application\TiposNorma\QueryHandlers;

use App\Application\TiposNorma\DTOs\TipoNormaDTO;
use App\Application\TiposNorma\Queries\GetTipoNormaByIdQuery;
use App\Domain\TiposNorma\Contracts\TipoNormaRepositoryInterface;

class GetTipoNormaByIdQueryHandler
{
    public function __construct(private readonly TipoNormaRepositoryInterface $repository) {}

    public function handle(GetTipoNormaByIdQuery $query): TipoNormaDTO
    {
        return $this->repository->findById($query->id);
    }
}
