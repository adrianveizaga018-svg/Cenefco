<?php

namespace App\Application\TiposDocumentoTransparencia\QueryHandlers;

use App\Application\TiposDocumentoTransparencia\DTOs\TipoDocumentoTransparenciaDTO;
use App\Application\TiposDocumentoTransparencia\Queries\GetTipoDocumentoTransparenciaByIdQuery;
use App\Domain\TiposDocumentoTransparencia\Contracts\TipoDocumentoTransparenciaRepositoryInterface;

class GetTipoDocumentoTransparenciaByIdQueryHandler
{
    public function __construct(private readonly TipoDocumentoTransparenciaRepositoryInterface $repository) {}

    public function handle(GetTipoDocumentoTransparenciaByIdQuery $query): TipoDocumentoTransparenciaDTO
    {
        return $this->repository->findById($query->id);
    }
}
