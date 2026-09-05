<?php

namespace App\Application\Honorarios\QueryHandlers;

use App\Application\Honorarios\Queries\GetHonorariosSugeridosDelMesQuery;
use App\Domain\Honorarios\Contracts\HonorarioSugeridoRepositoryInterface;

class GetHonorariosSugeridosDelMesQueryHandler
{
    public function __construct(private readonly HonorarioSugeridoRepositoryInterface $repository) {}

    public function handle(GetHonorariosSugeridosDelMesQuery $query): array
    {
        return $this->repository->docentesActivosDelMesConSugerido($query->anio, $query->mes);
    }
}
