<?php

namespace App\Domain\CampanasPublicidad\Contracts;

use App\Application\CampanasPublicidad\DTOs\CampanaMetricaDTO;
use App\Application\CampanasPublicidad\DTOs\CampanaPublicidadDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface CampanaPublicidadRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, array $filtros = []): array;

    public function findById(int $id): CampanaPublicidadDTO;

    public function create(array $data): CampanaPublicidadDTO;

    public function update(int $id, array $data): CampanaPublicidadDTO;

    public function delete(int $id): bool;

    public function tieneGastos(int $id): bool;

    public function registrarMetrica(int $campanaId, array $data): CampanaMetricaDTO;

    public function reportePorCurso(?string $fechaInicio, ?string $fechaFin): array;
}
