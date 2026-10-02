<?php

namespace App\Http\Controllers\BloquesAjustables;

use App\Application\BloquesAjustables\Commands\CreateBloquesAjustableCommand;
use App\Application\BloquesAjustables\Commands\DeleteBloquesAjustableCommand;
use App\Application\BloquesAjustables\Commands\UpdateBloquesAjustableCommand;
use App\Application\BloquesAjustables\Handlers\CreateBloquesAjustableHandler;
use App\Application\BloquesAjustables\Handlers\DeleteBloquesAjustableHandler;
use App\Application\BloquesAjustables\Handlers\UpdateBloquesAjustableHandler;
use App\Application\BloquesAjustables\Queries\GetBloquesAjustableByIdQuery;
use App\Application\BloquesAjustables\Queries\GetBloquesAjustablesQuery;
use App\Application\BloquesAjustables\QueryHandlers\GetBloquesAjustableByIdQueryHandler;
use App\Application\BloquesAjustables\QueryHandlers\GetBloquesAjustablesQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\BloquesAjustables\StoreBloquesAjustableRequest;
use App\Http\Requests\BloquesAjustables\UpdateBloquesAjustableRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BloquesAjustableController extends Controller
{
    public function __construct(
        private readonly GetBloquesAjustablesQueryHandler $listHandler,
        private readonly GetBloquesAjustableByIdQueryHandler $showHandler,
        private readonly CreateBloquesAjustableHandler $createHandler,
        private readonly UpdateBloquesAjustableHandler $updateHandler,
        private readonly DeleteBloquesAjustableHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 15),
        ]);

        return response()->json($this->listHandler->handle(new GetBloquesAjustablesQuery(
            pagination:         $pagination,
            idPagina:           $request->integer('id_pagina') ?: null,
            idBloqueplantilla:  $request->integer('id_bloqueplantilla') ?: null,
            conInactivos:       (bool) $request->get('conInactivos', false),
        )));
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->showHandler->handle(new GetBloquesAjustableByIdQuery($id)));
    }

    public function store(StoreBloquesAjustableRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateBloquesAjustableCommand(
            idBloqueajustable:  $request->integer('id_bloqueajustable'),
            idPagina:           $request->integer('id_pagina'),
            idBloqueplantilla:  $request->integer('id_bloqueplantilla'),
            nombre:             $request->input('nombre'),
            orden:              $request->integer('orden') ?: null,
            idUsReg:            auth()->id(),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateBloquesAjustableRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateBloquesAjustableCommand(
            idBloqueajustable:  $id,
            idPagina:           $request->integer('id_pagina') ?: null,
            idBloqueplantilla:  $request->integer('id_bloqueplantilla') ?: null,
            nombre:             $request->input('nombre'),
            orden:              $request->integer('orden') ?: null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteBloquesAjustableCommand($id));

        return response()->json(null, 204);
    }
}
