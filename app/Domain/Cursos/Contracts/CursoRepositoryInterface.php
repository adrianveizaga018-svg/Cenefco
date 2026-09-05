<?php

namespace App\Domain\Cursos\Contracts;

use App\Application\Cursos\DTOs\CursoDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface CursoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloPublicados = false, ?array $idsPermitidos = null): array;

    public function findById(int $id, bool $soloPublicados = false): CursoDTO;

    public function findBySlug(string $slug, bool $soloPublicados = false): CursoDTO;

    public function create(array $data): CursoDTO;

    public function update(int $id, array $data): CursoDTO;

    public function delete(int $id): bool;

    public function tieneInscritos(int $id): bool;

    public function docentesIndex(int $programaId, bool $soloVisibles = false): array;

    public function docentesByImp(int $idImp, bool $soloVisibles = false): array;

    public function docentesAttach(int $programaId, int $docenteId, int $orden): void;

    public function docentesDetach(int $programaId, int $docenteId): void;

    public function estadisticasGlobales(string $fechaInicio, string $fechaFin, ?array $idImpPermitidos = null): array;

    public function inscritosPorPeriodo(string $fechaInicio, string $fechaFin, ?array $idImpPermitidos = null): \Illuminate\Support\Collection;

    public function pagosPorPeriodo(string $fechaInicio, string $fechaFin, ?array $idImpPermitidos = null): \Illuminate\Support\Collection;

    public function estudiantesParaExportMoodle(int $idImp): array;

    public function idMatDeImparticion(int $idImp): ?int;

    public function planVinculadoAMateria(int $idMat): ?int;

    public function vincularPlanAMateria(int $idMat, int $idPlan, int $idUsReg): void;

    public function alertasCobrosPorImparticion(int $diasProximos, ?array $idImpPermitidos = null): array;
}
