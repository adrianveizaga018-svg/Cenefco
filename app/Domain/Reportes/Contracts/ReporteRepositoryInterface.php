<?php

namespace App\Domain\Reportes\Contracts;

use App\Application\Reportes\DTOs\ReporteVentasPorPeriodoDTO;

interface ReporteRepositoryInterface
{

    public function ventasPorPeriodo(string $fechaInicio, string $fechaFin, ?int $usuarioId): array;

    public function cuotasCurso(?int $idImp, ?string $fechaInicio, ?string $fechaFin, bool $conInactivos, ?array $idImpPermitidos = null): array;
}
