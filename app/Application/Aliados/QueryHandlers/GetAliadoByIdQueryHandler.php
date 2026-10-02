<?php

namespace App\Application\Aliados\QueryHandlers;

use App\Application\Aliados\DTOs\AliadoDTO;
use App\Application\Aliados\Queries\GetAliadoByIdQuery;
use App\Domain\Aliados\Contracts\AliadoRepositoryInterface;

class GetAliadoByIdQueryHandler
{
    public function __construct(private readonly AliadoRepositoryInterface $repository) {}

    public function handle(GetAliadoByIdQuery $query): AliadoDTO
    {
        return $this->repository->findById($query->id);
    }
}
