<?php

namespace App\Application\Acreditaciones\QueryHandlers;

use App\Application\Acreditaciones\Queries\GetAcreditacionesQuery;
use App\Domain\Acreditaciones\Contracts\AcreditacionRepositoryInterface;

class GetAcreditacionesQueryHandler
{
    public function __construct(
        private readonly AcreditacionRepositoryInterface $repository,
    ) {}

    public function handle(GetAcreditacionesQuery $query): array
    {
        return $this->repository->paginate(
            page:     $query->page,
            pageSize: $query->pageSize,
            query:    $query->query,
            activo:   $query->activo,
        );
    }
}
