<?php

namespace App\Application\Cursos\QueryHandlers;

use App\Application\Cursos\DTOs\CursoEstadisticasDTO;
use App\Application\Cursos\Queries\GetCursosEstadisticasQuery;
use App\Application\Cursos\Support\RangoPeriodoResolver;
use App\Domain\Cursos\Contracts\CursoRepositoryInterface;

class GetCursosEstadisticasQueryHandler
{
    public function __construct(
        private readonly CursoRepositoryInterface $repository
    ) {}

    public function handle(GetCursosEstadisticasQuery $query): CursoEstadisticasDTO
    {
        [$inicio, $fin] = RangoPeriodoResolver::resolver($query->periodo, $query->fecha, $query->fechaFin);

        $resultado = $this->repository->estadisticasGlobales(
            $inicio->toDateString(),
            $fin->toDateString(),
            $query->idImpPermitidos,
        );

        return new CursoEstadisticasDTO(
            periodo:      $query->periodo,
            fecha_inicio: $inicio->toDateString(),
            fecha_fin:    $fin->toDateString(),
            inscritos:    $resultado['inscritos'],
            ingresos:     $resultado['ingresos'],
        );
    }
}
