<?php

namespace App\Application\Cursos\QueryHandlers;

use App\Application\Cursos\DTOs\CursoEstadisticasDetalleDTO;
use App\Application\Cursos\Queries\GetCursosEstadisticasDetalleQuery;
use App\Application\Cursos\Support\RangoPeriodoResolver;
use App\Domain\Cursos\Contracts\CursoRepositoryInterface;

class GetCursosEstadisticasDetalleQueryHandler
{
    public function __construct(
        private readonly CursoRepositoryInterface $repository
    ) {}

    public function handle(GetCursosEstadisticasDetalleQuery $query): CursoEstadisticasDetalleDTO
    {
        [$inicio, $fin] = RangoPeriodoResolver::resolver($query->periodo, $query->fecha, $query->fechaFin);

        $fechaInicio = $inicio->toDateString();
        $fechaFin    = $fin->toDateString();

        $inscritos = $this->repository->inscritosPorPeriodo($fechaInicio, $fechaFin, $query->idImpPermitidos);
        $pagos     = $this->repository->pagosPorPeriodo($fechaInicio, $fechaFin, $query->idImpPermitidos);

        return new CursoEstadisticasDetalleDTO(
            periodo:         $query->periodo,
            fecha_inicio:    $fechaInicio,
            fecha_fin:       $fechaFin,
            total_inscritos: $inscritos->count(),
            total_ingresos:  (float) $pagos->sum('monto_pagado'),
            inscritos:       $inscritos->all(),
            pagos:           $pagos->all(),
        );
    }
}
