<?php

namespace App\Application\TiposPostgrado\QueryHandlers;

use App\Application\TiposPostgrado\Queries\GetTiposPostgradoQuery;
use App\Domain\TiposPostgrado\Contracts\TipoPostgradoRepositoryInterface;

class GetTiposPostgradoQueryHandler
{
    public function __construct(private readonly TipoPostgradoRepositoryInterface $repository) {}

    public function handle(GetTiposPostgradoQuery $query): array
    {
        return $this->repository->paginate(
            $query->pageIndex,
            $query->pageSize,
            $query->idPlan,
            $query->idTipopago,
            $query->conInactivos,
        );
    }
}
