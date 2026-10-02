<?php

namespace App\Http\Controllers\Api;

use App\Application\TiposNorma\Commands\CreateTipoNormaCommand;
use App\Application\TiposNorma\Commands\DeleteTipoNormaCommand;
use App\Application\TiposNorma\Commands\UpdateTipoNormaCommand;
use App\Application\TiposNorma\Handlers\CreateTipoNormaHandler;
use App\Application\TiposNorma\Handlers\DeleteTipoNormaHandler;
use App\Application\TiposNorma\Handlers\UpdateTipoNormaHandler;
use App\Application\TiposNorma\Queries\GetAllTiposNormaQuery;
use App\Application\TiposNorma\QueryHandlers\GetAllTiposNormaQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\TiposNorma\StoreTipoNormaRequest;
use App\Http\Requests\TiposNorma\UpdateTipoNormaRequest;
use Illuminate\Http\JsonResponse;

class TipoNormaController extends Controller
{
    public function __construct(
        private readonly GetAllTiposNormaQueryHandler $getAllHandler,
        private readonly CreateTipoNormaHandler $createHandler,
        private readonly UpdateTipoNormaHandler $updateHandler,
        private readonly DeleteTipoNormaHandler $deleteHandler,
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->getAllHandler->handle(new GetAllTiposNormaQuery));
    }

    public function store(StoreTipoNormaRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateTipoNormaCommand(
            nombre: $request->nombre,
            sigla: $request->sigla,
            descripcion: $request->descripcion,
            activo: $request->boolean('activo', true),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateTipoNormaRequest $request, int $id): JsonResponse
    {
        return response()->json($this->updateHandler->handle(new UpdateTipoNormaCommand($id, $request->validated())));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteTipoNormaCommand($id));

        return response()->json(null, 204);
    }
}
