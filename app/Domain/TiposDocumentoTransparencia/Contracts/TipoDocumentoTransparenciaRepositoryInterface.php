<?php

namespace App\Domain\TiposDocumentoTransparencia\Contracts;

use App\Application\TiposDocumentoTransparencia\DTOs\TipoDocumentoTransparenciaDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface TipoDocumentoTransparenciaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination): array;

    public function findById(int $id): TipoDocumentoTransparenciaDTO;

    public function create(array $data): TipoDocumentoTransparenciaDTO;

    public function update(int $id, array $data): TipoDocumentoTransparenciaDTO;

    public function delete(int|array $ids): bool;
}
