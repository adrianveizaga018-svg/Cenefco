<?php

namespace App\Application\CompromisosCobro\QueryHandlers;

use App\Application\CompromisosCobro\Queries\GetCompromisoCobroLogQuery;
use App\Domain\CompromisosCobro\Contracts\CompromisoCobroRepositoryInterface;
use Illuminate\Support\Collection;

class GetCompromisoCobroLogQueryHandler
{
    public function __construct(private readonly CompromisoCobroRepositoryInterface $repository) {}

    public function handle(GetCompromisoCobroLogQuery $query): Collection
    {
        return $this->repository->logsDe($query->compromisoCobroId);
    }
}
