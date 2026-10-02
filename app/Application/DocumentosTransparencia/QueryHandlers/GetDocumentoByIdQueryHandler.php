<?php

namespace App\Application\DocumentosTransparencia\QueryHandlers;

use App\Application\DocumentosTransparencia\DTOs\DocumentoDTO;
use App\Application\DocumentosTransparencia\Queries\GetDocumentoByIdQuery;
use App\Domain\DocumentosTransparencia\Contracts\DocumentoRepositoryInterface;

class GetDocumentoByIdQueryHandler
{
    public function __construct(private readonly DocumentoRepositoryInterface $repository) {}

    public function handle(GetDocumentoByIdQuery $query): DocumentoDTO
    {
        return $this->repository->findById($query->id);
    }
}
