<?php

namespace App\Application\TiposDocumentoTransparencia\QueryHandlers;

use App\Application\TiposDocumentoTransparencia\Queries\GetTiposDocumentoTransparenciaQuery;
use App\Domain\TiposDocumentoTransparencia\Contracts\TipoDocumentoTransparenciaRepositoryInterface;

class GetTiposDocumentoTransparenciaQueryHandler
{
    public function __construct(private readonly TipoDocumentoTransparenciaRepositoryInterface $repository) {}

    public function handle(GetTiposDocumentoTransparenciaQuery $query): array
    {
        return $this->repository->paginate($query->pagination);
    }
}
