<?php

namespace App\Application\CompromisosCobro\QueryHandlers;

use App\Application\CompromisosCobro\DTOs\CompromisoCobroDTO;
use App\Application\CompromisosCobro\Queries\GetCompromisoAbiertoPorInscripcionQuery;
use App\Domain\CompromisosCobro\Contracts\CompromisoCobroRepositoryInterface;

class GetCompromisoAbiertoPorInscripcionQueryHandler
{
    public function __construct(private readonly CompromisoCobroRepositoryInterface $repository) {}

    public function handle(GetCompromisoAbiertoPorInscripcionQuery $query): ?CompromisoCobroDTO
    {
        return $this->repository->findAbiertoPorInscripcion($query->idIns);
    }
}
