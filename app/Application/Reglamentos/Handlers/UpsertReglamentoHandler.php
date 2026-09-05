<?php

namespace App\Application\Reglamentos\Handlers;

use App\Application\Reglamentos\Commands\UpsertReglamentoCommand;
use App\Application\Reglamentos\DTOs\ReglamentoDTO;
use App\Domain\Reglamentos\Contracts\ReglamentoRepositoryInterface;

class UpsertReglamentoHandler
{
    public function __construct(
        private readonly ReglamentoRepositoryInterface $repository
    ) {}

    public function handle(UpsertReglamentoCommand $command): ReglamentoDTO
    {
        $data = array_filter([
            'bienvenida'        => $command->bienvenida,
            'reglas_asistencia' => $command->reglasAsistencia,
            'reglas_evaluacion' => $command->reglasEvaluacion,
            'reglas_pagos'      => $command->reglasPagos,
            'reglas_conducta'   => $command->reglasConducra,
            'reglas_plataformas'=> $command->reglasPlataformas,
            'reglas_derechos'   => $command->reglasDerechos,
        ], fn ($v) => $v !== null);

        $row = $this->repository->upsert($command->idPrograma, $data);

        return ReglamentoDTO::fromRow($command->idPrograma, $row);
    }
}
