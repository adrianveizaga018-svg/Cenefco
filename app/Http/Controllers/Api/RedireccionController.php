<?php

namespace App\Http\Controllers\Api;

use App\Application\Redirecciones\Commands\CreateRedireccionCommand;
use App\Application\Redirecciones\Commands\DeleteRedireccionCommand;
use App\Application\Redirecciones\Commands\UpdateRedireccionCommand;
use App\Application\Redirecciones\Handlers\CreateRedireccionHandler;
use App\Application\Redirecciones\Handlers\DeleteRedireccionHandler;
use App\Application\Redirecciones\Handlers\UpdateRedireccionHandler;
use App\Application\Redirecciones\Queries\GetRedireccionByIdQuery;
use App\Application\Redirecciones\Queries\GetRedireccionesQuery;
use App\Application\Redirecciones\QueryHandlers\GetRedireccionByIdQueryHandler;
use App\Application\Redirecciones\QueryHandlers\GetRedireccionesQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Redirecciones\StoreRedireccionRequest;
use App\Http\Requests\Redirecciones\UpdateRedireccionRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RedireccionController extends Controller
{
    public function __construct(
        private readonly GetRedireccionesQueryHandler $getRedireccionesHandler,
        private readonly GetRedireccionByIdQueryHandler $getByIdHandler,
        private readonly CreateRedireccionHandler $createHandler,
        private readonly UpdateRedireccionHandler $updateHandler,
        private readonly DeleteRedireccionHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 30),
            'query'     => $request->get('query', ''),
            'sortKey'   => $request->input('sort.key', 'url_origen'),
            'sortOrder' => $request->input('sort.order', 'asc'),
        ]);

        return response()->json($this->getRedireccionesHandler->handle(new GetRedireccionesQuery($pagination)));
    }

    public function store(StoreRedireccionRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateRedireccionCommand(
            url_origen:  $request->url_origen,
            url_destino: $request->url_destino,
            codigo_http: $request->integer('codigo_http', 301),
            activo:      $request->boolean('activo', true),
            notas:       $request->notas,
        ));

        return response()->json($dto, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->getByIdHandler->handle(new GetRedireccionByIdQuery($id)));
    }

    public function update(UpdateRedireccionRequest $request, int $id): JsonResponse
    {
        return response()->json($this->updateHandler->handle(new UpdateRedireccionCommand(
            id:          $id,
            url_origen:  $request->url_origen,
            url_destino: $request->url_destino,
            codigo_http: $request->has('codigo_http') ? $request->integer('codigo_http') : null,
            activo:      $request->has('activo') ? $request->boolean('activo') : null,
            notas:       $request->notas,
        )));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteRedireccionCommand($id));

        return response()->json(null, 204);
    }
}
