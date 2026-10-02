<?php

namespace App\Http\Controllers\Api;

use App\Application\Organigramas\Commands\CreateOrganigramaCommand;
use App\Application\Organigramas\Commands\DeleteOrganigramaCommand;
use App\Application\Organigramas\Commands\UpdateOrganigramaCommand;
use App\Application\Organigramas\Handlers\CreateOrganigramaHandler;
use App\Application\Organigramas\Handlers\DeleteOrganigramaHandler;
use App\Application\Organigramas\Handlers\UpdateOrganigramaHandler;
use App\Application\Organigramas\Queries\GetAllOrganigramasQuery;
use App\Application\Organigramas\Queries\GetLatestOrganigramaQuery;
use App\Application\Organigramas\QueryHandlers\GetAllOrganigramasQueryHandler;
use App\Application\Organigramas\QueryHandlers\GetLatestOrganigramaQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organigramas\StoreOrganigramaRequest;
use App\Http\Requests\Organigramas\UpdateOrganigramaRequest;
use Illuminate\Http\JsonResponse;

class OrganigramaController extends Controller
{
    public function __construct(
        private readonly GetAllOrganigramasQueryHandler $getAllHandler,
        private readonly GetLatestOrganigramaQueryHandler $getLatestHandler,
        private readonly CreateOrganigramaHandler $createHandler,
        private readonly UpdateOrganigramaHandler $updateHandler,
        private readonly DeleteOrganigramaHandler $deleteHandler,
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->getAllHandler->handle(new GetAllOrganigramasQuery));
    }

    public function latest(): JsonResponse
    {
        return response()->json($this->getLatestHandler->handle(new GetLatestOrganigramaQuery));
    }

    public function store(StoreOrganigramaRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateOrganigramaCommand(
            secretaria_id: (int) $request->secretaria_id,
            parent_id: $request->parent_id ? (int) $request->parent_id : null,
            nivel: $request->integer('nivel', 0),
            orden: $request->integer('orden', 0),
            imagen_url: $request->imagen_url,
            fecha_actualizacion: $request->fecha_actualizacion,
            vigente: $request->boolean('vigente', true),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateOrganigramaRequest $request, int $id): JsonResponse
    {
        return response()->json($this->updateHandler->handle(new UpdateOrganigramaCommand($id, $request->validated())));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteOrganigramaCommand($id));

        return response()->json(null, 204);
    }
}
