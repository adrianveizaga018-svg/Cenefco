<?php

namespace App\Http\Controllers\Api;

use App\Application\ManualesInstitucionales\Commands\CreateManualInstitucionalCommand;
use App\Application\ManualesInstitucionales\Handlers\CreateManualInstitucionalHandler;
use App\Application\ManualesInstitucionales\Queries\GetAllManualesQuery;
use App\Application\ManualesInstitucionales\QueryHandlers\GetAllManualesQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\ManualesInstitucionales\StoreManualRequest;
use Illuminate\Http\JsonResponse;

class ManualInstitucionalController extends Controller
{
    public function __construct(
        private readonly GetAllManualesQueryHandler $getAllHandler,
        private readonly CreateManualInstitucionalHandler $createHandler,
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->getAllHandler->handle(new GetAllManualesQuery));
    }

    public function store(StoreManualRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateManualInstitucionalCommand(
            tipo: $request->tipo,
            titulo: $request->titulo,
            descripcion: $request->descripcion,
            archivo_url: $request->archivo_url,
            formato: $request->formato,
            numero_paginas: $request->numero_paginas,
            gestion: $request->gestion,
            version: $request->get('version', 1),
            vigente: $request->boolean('vigente', true),
            fecha_publicacion: $request->fecha_publicacion,
            publicado_por: $request->user()?->id,
        ));

        return response()->json($dto, 201);
    }
}
