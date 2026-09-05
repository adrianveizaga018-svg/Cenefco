<?php

namespace App\Domain\Inscripciones\Contracts;

use App\Application\Inscripciones\DTOs\InscripcionDTO;
use App\Application\Inscripciones\DTOs\InscripcionDetalleDTO;
use App\Application\Inscripciones\DTOs\DocumentosInscripcionDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface InscripcionRepositoryInterface
{
    
    public function paginate(PaginationDTO $pagination, bool $conInactivos, ?int $idUs, ?int $idImp, ?int $programaId, ?string $periodo, ?string $gestion, ?array $idImpPermitidos = null): array;

    public function findById(int $id): InscripcionDTO;

    public function ciDeUsuario(int $idUs): ?string;

    public function findDetalleById(int $id): InscripcionDetalleDTO;

    public function getDocumentos(int $idIns): DocumentosInscripcionDTO;

    public function upsertDocumento(int $idUs, int $idFechadoc, array $data): object;

    public function existeInscripcionActiva(int $idUs, int $idImp): bool;

    public function verificarPagoCompleto(int $id): array;

    public function create(array $data): InscripcionDTO;

    public function update(int $id, array $data): InscripcionDTO;

    public function delete(int $id): void;
}
