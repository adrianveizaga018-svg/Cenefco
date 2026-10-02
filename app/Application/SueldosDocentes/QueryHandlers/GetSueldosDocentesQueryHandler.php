<?php

namespace App\Application\SueldosDocentes\QueryHandlers;

use App\Application\SueldosDocentes\Queries\GetSueldosDocentesQuery;
use App\Domain\SueldosDocentes\Contracts\SueldoDocenteRepositoryInterface;

class GetSueldosDocentesQueryHandler
{
    public function __construct(
        private readonly SueldoDocenteRepositoryInterface $repository,
    ) {}

    public function handle(GetSueldosDocentesQuery $query): array
    {
        return $this->repository->paginate($query->pagination, [
            'query'       => $query->pagination->query,
            'periodo'     => $query->periodo,
            'gestion'     => $query->gestion,
            'estado_pago' => $query->estadoPago,
        ]);
    }
}
