<?php

namespace App\Application\InscripcionesDiplomado\QueryHandlers;

use App\Application\InscripcionesDiplomado\Queries\GetInscripcionesDiplomadoQuery;
use App\Domain\InscripcionesDiplomado\Contracts\InscripcionDiplomadoRepositoryInterface;

class GetInscripcionesDiplomadoQueryHandler
{
    public function __construct(
        private readonly InscripcionDiplomadoRepositoryInterface $repository,
    ) {}

    public function handle(GetInscripcionesDiplomadoQuery $query): array
    {
        return $this->repository->paginate($query->pagination, [
            'estado'      => $query->estado,
            'programa_id' => $query->programaId,
        ]);
    }
}
