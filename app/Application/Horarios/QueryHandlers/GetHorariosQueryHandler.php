<?php

namespace App\Application\Horarios\QueryHandlers;

use App\Application\Horarios\Queries\GetHorariosQuery;
use App\Domain\Horarios\Contracts\HorarioRepositoryInterface;

class GetHorariosQueryHandler
{
    public function __construct(
        private readonly HorarioRepositoryInterface $repository
    ) {}

    public function handle(GetHorariosQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->conInactivos, $query->idImp);
    }
}
