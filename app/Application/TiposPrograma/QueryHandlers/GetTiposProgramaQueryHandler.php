<?php

namespace App\Application\TiposPrograma\QueryHandlers;

use App\Application\TiposPrograma\Queries\GetTiposProgramaQuery;
use App\Domain\TiposPrograma\Contracts\TipoProgramaRepositoryInterface;

class GetTiposProgramaQueryHandler
{
    public function __construct(private readonly TipoProgramaRepositoryInterface $repository) {}

    public function handle(GetTiposProgramaQuery $query): array
    {
        return $this->repository->paginate(
            $query->pageIndex,
            $query->pageSize,
            $query->query,
            $query->conInactivos,
        );
    }
}
