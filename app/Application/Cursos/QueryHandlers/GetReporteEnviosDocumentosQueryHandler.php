<?php

namespace App\Application\Cursos\QueryHandlers;

use App\Application\Cursos\DTOs\ReporteEnviosDocumentosDTO;
use App\Application\Cursos\Queries\GetReporteEnviosDocumentosQuery;
use App\Domain\EnviosCertificado\Contracts\EnvioCertificadoRepositoryInterface;

class GetReporteEnviosDocumentosQueryHandler
{
    public function __construct(
        private readonly EnvioCertificadoRepositoryInterface $repository
    ) {}

    public function handle(GetReporteEnviosDocumentosQuery $query): ReporteEnviosDocumentosDTO
    {
        $envios = $this->repository->porPeriodo($query->fechaInicio, $query->fechaFin, $query->idImpPermitidos);

        return new ReporteEnviosDocumentosDTO(
            fecha_inicio: $query->fechaInicio,
            fecha_fin:    $query->fechaFin,
            total:        $envios->count(),
            envios:       $envios->all(),
        );
    }
}
