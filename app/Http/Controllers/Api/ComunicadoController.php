<?php

namespace App\Http\Controllers\Api;

use App\Application\Comunicados\Commands\CreateComunicadoCommand;
use App\Application\Comunicados\Commands\DeleteComunicadoCommand;
use App\Application\Comunicados\Commands\UpdateComunicadoCommand;
use App\Application\Comunicados\Handlers\CreateComunicadoHandler;
use App\Application\Comunicados\Handlers\DeleteComunicadoHandler;
use App\Application\Comunicados\Handlers\UpdateComunicadoHandler;
use App\Application\Comunicados\Queries\GetComunicadoByIdQuery;
use App\Application\Comunicados\Queries\GetComunicadoBySlugQuery;
use App\Application\Comunicados\Queries\GetComunicadosQuery;
use App\Application\Comunicados\QueryHandlers\GetComunicadoByIdQueryHandler;
use App\Application\Comunicados\QueryHandlers\GetComunicadoBySlugQueryHandler;
use App\Application\Comunicados\QueryHandlers\GetComunicadosQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Comunicados\StoreComunicadoRequest;
use App\Http\Requests\Comunicados\UpdateComunicadoRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ComunicadoController extends Controller
{
    public function __construct(
        private readonly GetComunicadosQueryHandler $getComunicadosHandler,
        private readonly GetComunicadoByIdQueryHandler $getComunicadoByIdHandler,
        private readonly GetComunicadoBySlugQueryHandler $getComunicadoBySlugHandler,
        private readonly CreateComunicadoHandler $createHandler,
        private readonly UpdateComunicadoHandler $updateHandler,
        private readonly DeleteComunicadoHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $result = $this->getComunicadosHandler->handle(new GetComunicadosQuery(
            pageIndex: (int) $request->get('pageIndex', 1),
            pageSize: (int) $request->get('pageSize', 10),
            query: (string) $request->get('query', ''),
            estado: (string) $request->get('estado', ''),
            soloActivos: $request->boolean('soloActivos', false),
        ));

        return response()->json($result);
    }

    public function show(int $id): JsonResponse
    {
        $dto = $this->getComunicadoByIdHandler->handle(new GetComunicadoByIdQuery($id));

        return response()->json($dto);
    }

    public function showBySlug(string $slug): JsonResponse
    {
        $dto = $this->getComunicadoBySlugHandler->handle(new GetComunicadoBySlugQuery($slug));

        return response()->json($dto);
    }

    public function store(StoreComunicadoRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateComunicadoCommand(
            titulo: $request->titulo,
            resumen: $request->resumen,
            cuerpo: $request->cuerpo,
            imagen_url: $request->imagen_url,
            archivo_url: $request->archivo_url,
            estado: $request->estado ?? 'borrador',
            destacado: $request->boolean('destacado', false),
            fecha_publicacion: $request->fecha_publicacion,
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateComunicadoRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateComunicadoCommand(
            id: $id,
            titulo: $request->titulo,
            resumen: $request->resumen,
            cuerpo: $request->cuerpo,
            imagen_url: $request->imagen_url,
            archivo_url: $request->archivo_url,
            estado: $request->estado ?? 'borrador',
            destacado: $request->boolean('destacado', false),
            fecha_publicacion: $request->fecha_publicacion,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteComunicadoCommand($id));

        return response()->json(null, 204);
    }
}
