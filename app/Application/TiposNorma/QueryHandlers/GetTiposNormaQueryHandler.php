<?php

namespace App\Application\TiposNorma\QueryHandlers;

use App\Application\TiposNorma\Queries\GetTiposNormaQuery;
use App\Domain\TiposNorma\Contracts\TipoNormaRepositoryInterface;

class GetTiposNormaQueryHandler
{
    public function __construct(private readonly TipoNormaRepositoryInterface $repository) {}

    public function handle(GetTiposNormaQuery $query): array
    {
        return $this->repository->paginate($query->pagination);
    }
}
