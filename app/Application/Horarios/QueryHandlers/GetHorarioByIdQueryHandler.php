<?php

namespace App\Application\Horarios\QueryHandlers;

use App\Application\Horarios\DTOs\HorarioDTO;
use App\Application\Horarios\Queries\GetHorarioByIdQuery;
use App\Domain\Horarios\Contracts\HorarioRepositoryInterface;

class GetHorarioByIdQueryHandler
{
    public function __construct(
        private readonly HorarioRepositoryInterface $repository
    ) {}

    public function handle(GetHorarioByIdQuery $query): HorarioDTO
    {
        return $this->repository->findById($query->id);
    }
}
