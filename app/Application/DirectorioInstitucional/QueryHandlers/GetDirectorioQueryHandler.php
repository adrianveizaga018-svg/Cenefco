<?php

namespace App\Application\DirectorioInstitucional\QueryHandlers;

use App\Application\DirectorioInstitucional\Queries\GetDirectorioQuery;
use App\Domain\DirectorioInstitucional\Contracts\DirectorioInstitucionalRepositoryInterface;
use App\Shared\Kernel\DTOs\PaginationDTO;

class GetDirectorioQueryHandler
{
    public function __construct(
        private readonly DirectorioInstitucionalRepositoryInterface $repository
    ) {}

    public function handle(GetDirectorioQuery $query): array
    {
        $pagination = new PaginationDTO(
            pageIndex: $query->pageIndex,
            pageSize:  $query->pageSize,
            query:     $query->query,
            sortKey:   'orden',
            sortOrder: 'asc',
        );

        $soloActivos = $query->activo === '1' || $query->activo === 'true';

        return $this->repository->paginate($pagination, $soloActivos);
    }
}
