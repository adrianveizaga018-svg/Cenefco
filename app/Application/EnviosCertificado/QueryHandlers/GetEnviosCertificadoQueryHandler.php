<?php

namespace App\Application\EnviosCertificado\QueryHandlers;

use App\Application\EnviosCertificado\Queries\GetEnviosCertificadoQuery;
use App\Domain\EnviosCertificado\Contracts\EnvioCertificadoRepositoryInterface;

class GetEnviosCertificadoQueryHandler
{
    public function __construct(private readonly EnvioCertificadoRepositoryInterface $repository) {}

    public function handle(GetEnviosCertificadoQuery $query): array
    {
        return $this->repository->findByInscripcion($query->id_ins);
    }
}
