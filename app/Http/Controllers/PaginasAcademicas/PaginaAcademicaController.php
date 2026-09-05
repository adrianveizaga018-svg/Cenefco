<?php

namespace App\Http\Controllers\PaginasAcademicas;

use App\Application\PaginasAcademicas\Commands\CreatePaginaAcademicaCommand;
use App\Application\PaginasAcademicas\Commands\DeletePaginaAcademicaCommand;
use App\Application\PaginasAcademicas\Commands\UpdatePaginaAcademicaCommand;
use App\Application\PaginasAcademicas\Handlers\CreatePaginaAcademicaHandler;
use App\Application\PaginasAcademicas\Handlers\DeletePaginaAcademicaHandler;
use App\Application\PaginasAcademicas\Handlers\UpdatePaginaAcademicaHandler;
use App\Application\PaginasAcademicas\Queries\GetPaginaAcademicaByIdQuery;
use App\Application\PaginasAcademicas\Queries\GetPaginasAcademicasQuery;
use App\Application\PaginasAcademicas\QueryHandlers\GetPaginaAcademicaByIdQueryHandler;
use App\Application\PaginasAcademicas\QueryHandlers\GetPaginasAcademicasQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaginasAcademicas\StorePaginaAcademicaRequest;
use App\Http\Requests\PaginasAcademicas\UpdatePaginaAcademicaRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaginaAcademicaController extends Controller
{
    public function __construct(
        private readonly GetPaginasAcademicasQueryHandler $listHandler,
        private readonly GetPaginaAcademicaByIdQueryHandler $showHandler,
        private readonly CreatePaginaAcademicaHandler $createHandler,
        private readonly UpdatePaginaAcademicaHandler $updateHandler,
        private readonly DeletePaginaAcademicaHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 15),
        ]);

        return response()->json($this->listHandler->handle(new GetPaginasAcademicasQuery(
            pagination:   $pagination,
            conInactivos: (bool) $request->get('conInactivos', false),
        )));
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->showHandler->handle(new GetPaginaAcademicaByIdQuery($id)));
    }

    public function store(StorePaginaAcademicaRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreatePaginaAcademicaCommand(
            idPagina:    $request->integer('id_pagina'),
            nombre:      $request->string('nombre')->toString(),
            descripcion: $request->input('descripcion'),
            url:         $request->input('url'),
            idMod:       $request->integer('id_mod') ?: null,
            idUsReg:     auth()->id(),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdatePaginaAcademicaRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdatePaginaAcademicaCommand(
            idPagina:    $id,
            nombre:      $request->input('nombre'),
            descripcion: $request->input('descripcion'),
            url:         $request->input('url'),
            idMod:       $request->integer('id_mod') ?: null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeletePaginaAcademicaCommand($id));

        return response()->json(null, 204);
    }
}
