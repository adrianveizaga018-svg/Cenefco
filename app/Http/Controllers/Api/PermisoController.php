<?php

namespace App\Http\Controllers\Api;

use App\Application\Permisos\Commands\CreatePermisoCommand;
use App\Application\Permisos\Commands\DeletePermisoCommand;
use App\Application\Permisos\Commands\UpdatePermisoCommand;
use App\Application\Permisos\Handlers\CreatePermisoHandler;
use App\Application\Permisos\Handlers\DeletePermisoHandler;
use App\Application\Permisos\Handlers\UpdatePermisoHandler;
use App\Application\Permisos\Queries\GetPermisoByIdQuery;
use App\Application\Permisos\Queries\GetPermisosQuery;
use App\Application\Permisos\QueryHandlers\GetPermisoByIdQueryHandler;
use App\Application\Permisos\QueryHandlers\GetPermisosQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Permisos\StorePermisoRequest;
use App\Http\Requests\Permisos\UpdatePermisoRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PermisoController extends Controller
{
    public function __construct(
        private readonly GetPermisosQueryHandler $getPermisosHandler,
        private readonly GetPermisoByIdQueryHandler $getPermisoByIdHandler,
        private readonly CreatePermisoHandler $createHandler,
        private readonly UpdatePermisoHandler $updateHandler,
        private readonly DeletePermisoHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize' => $request->get('pageSize', 50),
            'query' => $request->get('query', ''),
            'sortKey' => $request->input('sort.key', 'codigo'),
            'sortOrder' => $request->input('sort.order', 'asc'),
        ]);

        return response()->json(
            $this->getPermisosHandler->handle(new GetPermisosQuery($pagination))
        );
    }

    public function store(StorePermisoRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreatePermisoCommand(
            codigo: $request->codigo,
            descripcion: $request->descripcion,
            modulo: $request->modulo,
        ));

        return response()->json($dto, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->getPermisoByIdHandler->handle(new GetPermisoByIdQuery($id))
        );
    }

    public function update(UpdatePermisoRequest $request, int $id): JsonResponse
    {
        return response()->json(
            $this->updateHandler->handle(new UpdatePermisoCommand($id, $request->validated()))
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeletePermisoCommand($id));

        return response()->json(null, 204);
    }
}
