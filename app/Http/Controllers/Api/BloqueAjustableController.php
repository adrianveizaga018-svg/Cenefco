<?php
namespace App\Http\Controllers\Api;
use App\Application\BloquesAjustables\Commands\CreateBloqueAjustableCommand;
use App\Application\BloquesAjustables\Commands\DeleteBloqueAjustableCommand;
use App\Application\BloquesAjustables\Commands\UpdateBloqueAjustableCommand;
use App\Application\BloquesAjustables\Handlers\CreateBloqueAjustableHandler;
use App\Application\BloquesAjustables\Handlers\DeleteBloqueAjustableHandler;
use App\Application\BloquesAjustables\Handlers\UpdateBloqueAjustableHandler;
use App\Application\BloquesAjustables\Queries\GetBloqueAjustableByIdQuery;
use App\Application\BloquesAjustables\Queries\GetBloquesAjustablesQuery;
use App\Application\BloquesAjustables\QueryHandlers\GetBloqueAjustableByIdQueryHandler;
use App\Application\BloquesAjustables\QueryHandlers\GetBloquesAjustablesQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\BloquesAjustables\StoreBloqueAjustableRequest;
use App\Http\Requests\BloquesAjustables\UpdateBloqueAjustableRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class BloqueAjustableController extends Controller {
    public function __construct(
        private readonly GetBloquesAjustablesQueryHandler $listHandler,
        private readonly GetBloqueAjustableByIdQueryHandler $showHandler,
        private readonly CreateBloqueAjustableHandler $createHandler,
        private readonly UpdateBloqueAjustableHandler $updateHandler,
        private readonly DeleteBloqueAjustableHandler $deleteHandler,
    ) {}
    public function index(Request $request): JsonResponse {
        $pagination = PaginationDTO::fromArray(['pageIndex' => $request->get('pageIndex', 1), 'pageSize' => $request->get('pageSize', 15)]);
        return response()->json($this->listHandler->handle(new GetBloquesAjustablesQuery(
            pagination: $pagination,
            idPagina: $request->integer('id_pagina') ?: null,
            idBloqueplantilla: $request->integer('id_bloqueplantilla') ?: null,
            conInactivos: $request->boolean('con_inactivos'),
        )));
    }
    public function show(int $id): JsonResponse {
        return response()->json($this->showHandler->handle(new GetBloqueAjustableByIdQuery($id)));
    }
    public function store(StoreBloqueAjustableRequest $request): JsonResponse {
        $dto = $this->createHandler->handle(new CreateBloqueAjustableCommand(
            idBloqueajustable: $request->integer('id_bloqueajustable'),
            idPagina: $request->integer('id_pagina') ?: null,
            idBloqueplantilla: $request->integer('id_bloqueplantilla') ?: null,
            titulo: $request->input('titulo'),
            idUsReg: auth()->id(),
        ));
        return response()->json($dto, 201);
    }
    public function update(UpdateBloqueAjustableRequest $request, int $id): JsonResponse {
        $this->updateHandler->handle(new UpdateBloqueAjustableCommand(idBloqueajustable: $id, idPagina: $request->integer('id_pagina') ?: null, idBloqueplantilla: $request->integer('id_bloqueplantilla') ?: null, titulo: $request->input('titulo')));
        return response()->json($this->showHandler->handle(new GetBloqueAjustableByIdQuery($id)));
    }
    public function destroy(int $id): JsonResponse {
        $this->deleteHandler->handle(new DeleteBloqueAjustableCommand($id));
        return response()->json(null, 204);
    }
}
