<?php

namespace App\Domain\InscripcionesDiplomado\Contracts;

use App\Application\InscripcionesDiplomado\DTOs\InscripcionDiplomadoDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface InscripcionDiplomadoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, array $filters = []): array;

    public function findById(int $id): InscripcionDiplomadoDTO;

    public function buscarDuplicado(string $ci, ?int $programaId): ?string;

    public function create(array $data): InscripcionDiplomadoDTO;

    public function update(int $id, array $data): InscripcionDiplomadoDTO;

    public function delete(int $id): void;

    public function convertirInscripcion(int $id, ?int $idImpOverride, ?int $idPlanOverride): array;
}
