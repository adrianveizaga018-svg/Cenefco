<?php

namespace App\Domain\CertVerificaciones\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface CertVerificacionRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?int $certificadoId, ?string $codigoConsultado, ?string $resultado): array;
    public function findById(int $id): mixed;
    public function create(array $data): mixed;
}
