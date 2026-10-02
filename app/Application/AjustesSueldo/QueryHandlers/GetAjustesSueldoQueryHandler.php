<?php

namespace App\Application\AjustesSueldo\QueryHandlers;

use App\Application\AjustesSueldo\Queries\GetAjustesSueldoQuery;
use App\Domain\AjustesSueldo\Contracts\AjusteSueldoRepositoryInterface;

class GetAjustesSueldoQueryHandler
{
    public function __construct(
        private readonly AjusteSueldoRepositoryInterface $repository,
    ) {}

    public function handle(GetAjustesSueldoQuery $query): array
    {
        return $this->repository->paginate([
            'pagination'  => $query->pagination,
            'empleado_id' => $query->empleadoId,
            'anio'        => $query->anio,
            'mes'         => $query->mes,
            'aplicado'    => $query->aplicado,
        ]);
    }
}
