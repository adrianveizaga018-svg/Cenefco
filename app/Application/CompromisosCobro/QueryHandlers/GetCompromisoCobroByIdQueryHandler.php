<?php

namespace App\Application\CompromisosCobro\QueryHandlers;

use App\Application\CompromisosCobro\DTOs\CompromisoCobroDTO;
use App\Application\CompromisosCobro\Queries\GetCompromisoCobroByIdQuery;
use App\Domain\CompromisosCobro\Contracts\CompromisoCobroRepositoryInterface;

class GetCompromisoCobroByIdQueryHandler
{
    public function __construct(private readonly CompromisoCobroRepositoryInterface $repository) {}

    public function handle(GetCompromisoCobroByIdQuery $query): CompromisoCobroDTO
    {
        return $this->repository->findById($query->id);
    }
}
