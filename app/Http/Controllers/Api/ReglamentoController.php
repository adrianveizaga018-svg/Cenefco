<?php

namespace App\Http\Controllers\Api;

use App\Application\Reglamentos\Commands\UpsertReglamentoCommand;
use App\Application\Reglamentos\Handlers\UpsertReglamentoHandler;
use App\Application\Reglamentos\Queries\GetReglamentoByProgramaQuery;
use App\Application\Reglamentos\QueryHandlers\GetReglamentoByProgramaQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Reglamentos\UpdateReglamentoRequest;
use Illuminate\Http\JsonResponse;

class ReglamentoController extends Controller
{
    public function __construct(
        private readonly GetReglamentoByProgramaQueryHandler $getByProgramaHandler,
        private readonly UpsertReglamentoHandler             $upsertHandler,
    ) {}

    public function show(int $idPrograma): JsonResponse
    {
        return response()->json(
            $this->getByProgramaHandler->handle(new GetReglamentoByProgramaQuery($idPrograma))
        );
    }

    public function update(UpdateReglamentoRequest $request, int $idPrograma): JsonResponse
    {
        return response()->json(
            $this->upsertHandler->handle(new UpsertReglamentoCommand(
                idPrograma:        $idPrograma,
                bienvenida:        $request->input('bienvenida'),
                reglasAsistencia:  $request->input('reglas_asistencia'),
                reglasEvaluacion:  $request->input('reglas_evaluacion'),
                reglasPagos:       $request->input('reglas_pagos'),
                reglasConducra:    $request->input('reglas_conducta'),
                reglasPlataformas: $request->input('reglas_plataformas'),
                reglasDerechos:    $request->input('reglas_derechos'),
            ))
        );
    }
}
