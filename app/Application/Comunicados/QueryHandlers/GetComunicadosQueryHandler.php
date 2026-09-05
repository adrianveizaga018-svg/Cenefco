<?php

namespace App\Application\Comunicados\QueryHandlers;

use App\Application\Comunicados\Queries\GetComunicadosQuery;
use App\Domain\Comunicados\Contracts\ComunicadoRepositoryInterface;
use App\Shared\Kernel\DTOs\PaginationDTO;

class GetComunicadosQueryHandler
{
    public function __construct(
        private readonly ComunicadoRepositoryInterface $repository
    ) {}

    public function handle(GetComunicadosQuery $query): array
    {
        $pagination = new PaginationDTO(
            pageIndex: $query->pageIndex,
            pageSize:  $query->pageSize,
            query:     $query->query,
            sortKey:   'created_at',
            sortOrder: 'desc',
        );

        return $this->repository->paginate($pagination, $query->soloActivos);
    }
}
