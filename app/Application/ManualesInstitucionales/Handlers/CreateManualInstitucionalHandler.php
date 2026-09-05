<?php

namespace App\Application\ManualesInstitucionales\Handlers;

use App\Application\ManualesInstitucionales\Commands\CreateManualInstitucionalCommand;
use App\Application\ManualesInstitucionales\DTOs\ManualInstitucionalDTO;
use App\Domain\ManualesInstitucionales\Contracts\ManualInstitucionalRepositoryInterface;

class CreateManualInstitucionalHandler
{
    public function __construct(private readonly ManualInstitucionalRepositoryInterface $repository) {}

    public function handle(CreateManualInstitucionalCommand $command): ManualInstitucionalDTO
    {
        return $this->repository->create([
            'tipo' => $command->tipo,
            'titulo' => $command->titulo,
            'descripcion' => $command->descripcion,
            'archivo_url' => $command->archivo_url,
            'formato' => $command->formato,
            'numero_paginas' => $command->numero_paginas,
            'gestion' => $command->gestion,
            'version' => $command->version,
            'vigente' => $command->vigente,
            'fecha_publicacion' => $command->fecha_publicacion,
            'publicado_por' => $command->publicado_por,
        ]);
    }
}
