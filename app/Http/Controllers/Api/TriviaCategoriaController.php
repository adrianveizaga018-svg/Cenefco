<?php

namespace App\Http\Controllers\Api;

use App\Application\Trivia\Commands\CreateTriviaCategoriaCommand;
use App\Application\Trivia\Commands\DeleteTriviaCategoriaCommand;
use App\Application\Trivia\Commands\UpdateTriviaCategoriaCommand;
use App\Application\Trivia\Handlers\CreateTriviaCategoriaHandler;
use App\Application\Trivia\Handlers\DeleteTriviaCategoriaHandler;
use App\Application\Trivia\Handlers\UpdateTriviaCategoriaHandler;
use App\Application\Trivia\Queries\GetTriviaCategoriaByIdQuery;
use App\Application\Trivia\Queries\GetTriviaCategoriaBySlugQuery;
use App\Application\Trivia\Queries\GetTriviaCategoriasQuery;
use App\Application\Trivia\QueryHandlers\GetTriviaCategoriaByIdQueryHandler;
use App\Application\Trivia\QueryHandlers\GetTriviaCategoriaBySlugQueryHandler;
use App\Application\Trivia\QueryHandlers\GetTriviaCategoriasQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Trivia\StoreTriviaCategoriaRequest;
use App\Http\Requests\Trivia\UpdateTriviaCategoriaRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TriviaCategoriaController extends Controller
{
    public function __construct(
        private readonly GetTriviaCategoriasQueryHandler $getCategoriasHandler,
        private readonly GetTriviaCategoriaByIdQueryHandler $getCategoriaByIdHandler,
        private readonly GetTriviaCategoriaBySlugQueryHandler $getCategoriaBySlugHandler,
        private readonly CreateTriviaCategoriaHandler $createHandler,
        private readonly UpdateTriviaCategoriaHandler $updateHandler,
        private readonly DeleteTriviaCategoriaHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize' => $request->get('pageSize', 15),
            'query' => $request->get('query', ''),
            'sortKey' => $request->input('sort.key', 'orden'),
            'sortOrder' => $request->input('sort.order', 'asc'),
        ]);

        return response()->json(
            $this->getCategoriasHandler->handle(
                new GetTriviaCategoriasQuery($pagination, $request->boolean('soloActivos', false))
            )
        );
    }

    public function store(StoreTriviaCategoriaRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateTriviaCategoriaCommand(
            nombre: $request->nombre,
            descripcion: $request->descripcion,
            imagen_url: $request->imagen_url,
            color: $request->color,
            curso_id: $request->curso_id,
            orden: (int) $request->get('orden', 0),
            activo: $request->boolean('activo', true),
        ));

        return response()->json($dto, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->getCategoriaByIdHandler->handle(new GetTriviaCategoriaByIdQuery($id)));
    }

    public function showBySlug(string $slug): JsonResponse
    {
        return response()->json($this->getCategoriaBySlugHandler->handle(new GetTriviaCategoriaBySlugQuery($slug)));
    }

    public function update(UpdateTriviaCategoriaRequest $request, int $id): JsonResponse
    {
        return response()->json($this->updateHandler->handle(
            new UpdateTriviaCategoriaCommand($id, $request->validated())
        ));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteTriviaCategoriaCommand($id));

        return response()->json(null, 204);
    }
}
