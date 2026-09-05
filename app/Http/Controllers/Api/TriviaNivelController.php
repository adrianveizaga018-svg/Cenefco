<?php

namespace App\Http\Controllers\Api;

use App\Application\Trivia\Commands\CreateTriviaNivelCommand;
use App\Application\Trivia\Commands\DeleteTriviaNivelCommand;
use App\Application\Trivia\Commands\UpdateTriviaNivelCommand;
use App\Application\Trivia\Handlers\CreateTriviaNivelHandler;
use App\Application\Trivia\Handlers\DeleteTriviaNivelHandler;
use App\Application\Trivia\Handlers\UpdateTriviaNivelHandler;
use App\Application\Trivia\Queries\GetTriviaNivelByIdQuery;
use App\Application\Trivia\Queries\GetTriviaNivelesQuery;
use App\Application\Trivia\QueryHandlers\GetTriviaNivelByIdQueryHandler;
use App\Application\Trivia\QueryHandlers\GetTriviaNivelesQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Trivia\StoreTriviaNivelRequest;
use App\Http\Requests\Trivia\UpdateTriviaNivelRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TriviaNivelController extends Controller
{
    public function __construct(
        private readonly GetTriviaNivelesQueryHandler $getNivelesHandler,
        private readonly GetTriviaNivelByIdQueryHandler $getNivelByIdHandler,
        private readonly CreateTriviaNivelHandler $createHandler,
        private readonly UpdateTriviaNivelHandler $updateHandler,
        private readonly DeleteTriviaNivelHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $this->getNivelesHandler->handle(new GetTriviaNivelesQuery(
                categoriaId: (int) $request->query('categoria_id'),
                soloActivos: $request->boolean('soloActivos', false),
            ))
        );
    }

    public function store(StoreTriviaNivelRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateTriviaNivelCommand(
            categoria_id: $request->categoria_id,
            nombre: $request->nombre,
            orden: (int) $request->get('orden', 0),
            puntaje_base: (int) $request->get('puntaje_base', 100),
            activo: $request->boolean('activo', true),
        ));

        return response()->json($dto, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->getNivelByIdHandler->handle(new GetTriviaNivelByIdQuery($id)));
    }

    public function update(UpdateTriviaNivelRequest $request, int $id): JsonResponse
    {
        return response()->json($this->updateHandler->handle(
            new UpdateTriviaNivelCommand($id, $request->validated())
        ));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteTriviaNivelCommand($id));

        return response()->json(null, 204);
    }
}
