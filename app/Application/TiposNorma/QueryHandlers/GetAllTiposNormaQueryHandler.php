<?php

namespace App\Application\TiposNorma\QueryHandlers;

use App\Application\TiposNorma\Queries\GetAllTiposNormaQuery;
use App\Domain\TiposNorma\Contracts\TipoNormaRepositoryInterface;

class GetAllTiposNormaQueryHandler
{
    public function __construct(private readonly TipoNormaRepositoryInterface $repository) {}

    public function handle(GetAllTiposNormaQuery $query): array
    {
        return $this->repository->all();
    }
}
