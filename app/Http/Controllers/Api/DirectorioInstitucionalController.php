<?php

namespace App\Http\Controllers\Api;

use App\Application\DirectorioInstitucional\Commands\CreateDirectorioCommand;
use App\Application\DirectorioInstitucional\Commands\DeleteDirectorioCommand;
use App\Application\DirectorioInstitucional\Commands\UpdateDirectorioCommand;
use App\Application\DirectorioInstitucional\Handlers\CreateDirectorioHandler;
use App\Application\DirectorioInstitucional\Handlers\DeleteDirectorioHandler;
use App\Application\DirectorioInstitucional\Handlers\UpdateDirectorioHandler;
use App\Application\DirectorioInstitucional\Queries\GetDirectorioByIdQuery;
use App\Application\DirectorioInstitucional\Queries\GetDirectorioQuery;
use App\Application\DirectorioInstitucional\QueryHandlers\GetDirectorioByIdQueryHandler;
use App\Application\DirectorioInstitucional\QueryHandlers\GetDirectorioQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\DirectorioInstitucional\StoreDirectorioRequest;
use App\Http\Requests\DirectorioInstitucional\UpdateDirectorioRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DirectorioInstitucionalController extends Controller
{
    public function __construct(
        private readonly GetDirectorioQueryHandler $getListHandler,
        private readonly GetDirectorioByIdQueryHandler $getByIdHandler,
        private readonly CreateDirectorioHandler $createHandler,
        private readonly UpdateDirectorioHandler $updateHandler,
        private readonly DeleteDirectorioHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $result = $this->getListHandler->handle(new GetDirectorioQuery(
            pageIndex: (int) $request->get('pageIndex', 1),
            pageSize: (int) $request->get('pageSize', 20),
            query: (string) $request->get('query', ''),
            activo: (string) $request->get('activo', ''),
        ));

        return response()->json($result);
    }

    public function show(int $id): JsonResponse
    {
        $dto = $this->getByIdHandler->handle(new GetDirectorioByIdQuery($id));

        return response()->json($dto);
    }

    public function store(StoreDirectorioRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateDirectorioCommand(
            secretaria_id: $request->secretaria_id,
            nombre: $request->nombre,
            descripcion: $request->descripcion,
            responsable: $request->responsable,
            cargo_responsable: $request->cargo_responsable,
            telefono: $request->telefono,
            telefono_interno: $request->telefono_interno,
            email: $request->email,
            foto_url: $request->foto_url,
            ubicacion: $request->ubicacion,
            horario: $request->horario,
            orden: (int) ($request->orden ?? 0),
            activo: (bool) ($request->activo ?? true),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateDirectorioRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateDirectorioCommand(
            id: $id,
            secretaria_id: $request->secretaria_id,
            nombre: $request->nombre,
            descripcion: $request->descripcion,
            responsable: $request->responsable,
            cargo_responsable: $request->cargo_responsable,
            telefono: $request->telefono,
            telefono_interno: $request->telefono_interno,
            email: $request->email,
            foto_url: $request->foto_url,
            ubicacion: $request->ubicacion,
            horario: $request->horario,
            orden: (int) ($request->orden ?? 0),
            activo: (bool) ($request->activo ?? true),
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteDirectorioCommand($id));

        return response()->json(null, 204);
    }
}
