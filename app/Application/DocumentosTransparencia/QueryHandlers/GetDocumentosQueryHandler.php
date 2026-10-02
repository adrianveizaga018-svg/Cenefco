<?php

namespace App\Application\DocumentosTransparencia\QueryHandlers;

use App\Application\DocumentosTransparencia\Queries\GetDocumentosQuery;
use App\Domain\DocumentosTransparencia\Contracts\DocumentoRepositoryInterface;

class GetDocumentosQueryHandler
{
    public function __construct(private readonly DocumentoRepositoryInterface $repository) {}

    public function handle(GetDocumentosQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->soloActivos);
    }
}
