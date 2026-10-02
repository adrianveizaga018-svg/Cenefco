<?php

namespace App\Http\Controllers\Api;

use App\Application\SpeechVentas\Commands\CreateSpeechVentasCommand;
use App\Application\SpeechVentas\Commands\DeleteSpeechVentasCommand;
use App\Application\SpeechVentas\Commands\UpdateSpeechVentasCommand;
use App\Application\SpeechVentas\Handlers\CreateSpeechVentasHandler;
use App\Application\SpeechVentas\Handlers\DeleteSpeechVentasHandler;
use App\Application\SpeechVentas\Handlers\UpdateSpeechVentasHandler;
use App\Application\SpeechVentas\Queries\GetSpeechVentasByIdQuery;
use App\Application\SpeechVentas\Queries\GetSpeechVentasQuery;
use App\Application\SpeechVentas\QueryHandlers\GetSpeechVentasByIdQueryHandler;
use App\Application\SpeechVentas\QueryHandlers\GetSpeechVentasQueryHandler;
use App\Domain\SpeechVentas\Contracts\SpeechVentasRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\SpeechVentas\StoreSpeechVentasRequest;
use App\Http\Requests\SpeechVentas\UpdateSpeechVentasRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SpeechVentasController extends Controller
{
    public function __construct(
        private readonly GetSpeechVentasQueryHandler $listHandler,
        private readonly GetSpeechVentasByIdQueryHandler $showHandler,
        private readonly CreateSpeechVentasHandler $createHandler,
        private readonly UpdateSpeechVentasHandler $updateHandler,
        private readonly DeleteSpeechVentasHandler $deleteHandler,
        private readonly SpeechVentasRepositoryInterface $speechRepo,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $activo = ($request->input('activo') !== null && $request->input('activo') !== '')
            ? (bool) $request->get('activo')
            : null;

        return response()->json($this->listHandler->handle(new GetSpeechVentasQuery(
            pageIndex: (int) $request->get('pageIndex', 1),
            pageSize:  (int) $request->get('pageSize', 20),
            query:     $request->input('query'),
            categoria: $request->input('categoria'),
            activo:    $activo,
        )));
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->showHandler->handle(new GetSpeechVentasByIdQuery($id)));
    }

    public function store(StoreSpeechVentasRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateSpeechVentasCommand(
            titulo:        $request->string('titulo')->toString(),
            categoria:     $request->input('categoria'),
            contenido:     $request->string('contenido')->toString(),
            palabrasClave: $request->input('palabras_clave'),
            activo:        $request->boolean('activo', true),
            orden:         $request->integer('orden', 0),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateSpeechVentasRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateSpeechVentasCommand(
            id:            $id,
            titulo:        $request->string('titulo')->toString(),
            categoria:     $request->input('categoria'),
            contenido:     $request->string('contenido')->toString(),
            palabrasClave: $request->input('palabras_clave'),
            activo:        $request->boolean('activo', true),
            orden:         $request->integer('orden', 0),
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteSpeechVentasCommand($id));

        return response()->json(null, 204);
    }

    public function toggleActivo(int $id): JsonResponse
    {
        $row = $this->speechRepo->toggleActivo($id);

        return response()->json($row);
    }

    public function categorias(): JsonResponse
    {
        return response()->json($this->speechRepo->getCategorias());
    }

    public function publicIndex(Request $request): JsonResponse
    {
        return response()->json($this->speechRepo->publicIndex(
            $request->input('q'),
            $request->input('categoria'),
        ));
    }
}
