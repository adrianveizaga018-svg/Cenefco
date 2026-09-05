<?php

namespace App\Http\Controllers\Api;

use App\Application\Trivia\Commands\CreateTriviaPreguntaCommand;
use App\Application\Trivia\Commands\DeleteTriviaPreguntaCommand;
use App\Application\Trivia\Commands\UpdateTriviaPreguntaCommand;
use App\Application\Trivia\Handlers\CreateTriviaPreguntaHandler;
use App\Application\Trivia\Handlers\DeleteTriviaPreguntaHandler;
use App\Application\Trivia\Handlers\UpdateTriviaPreguntaHandler;
use App\Application\Trivia\Queries\GetTriviaPreguntaByIdQuery;
use App\Application\Trivia\Queries\GetTriviaPreguntasQuery;
use App\Application\Trivia\QueryHandlers\GetTriviaPreguntaByIdQueryHandler;
use App\Application\Trivia\QueryHandlers\GetTriviaPreguntasQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Trivia\StoreTriviaPreguntaRequest;
use App\Http\Requests\Trivia\UpdateTriviaPreguntaRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TriviaPreguntaController extends Controller
{
    public function __construct(
        private readonly GetTriviaPreguntasQueryHandler $getPreguntasHandler,
        private readonly GetTriviaPreguntaByIdQueryHandler $getPreguntaByIdHandler,
        private readonly CreateTriviaPreguntaHandler $createHandler,
        private readonly UpdateTriviaPreguntaHandler $updateHandler,
        private readonly DeleteTriviaPreguntaHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize' => $request->get('pageSize', 15),
            'query' => $request->get('query', ''),
            'sortKey' => $request->input('sort.key', 'created_at'),
            'sortOrder' => $request->input('sort.order', 'desc'),
        ]);

        return response()->json(
            $this->getPreguntasHandler->handle(new GetTriviaPreguntasQuery(
                pagination: $pagination,
                categoriaId: $request->filled('categoria_id') ? (int) $request->query('categoria_id') : null,
                nivelId: $request->filled('nivel_id') ? (int) $request->query('nivel_id') : null,
            ))
        );
    }

    public function store(StoreTriviaPreguntaRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateTriviaPreguntaCommand(
            categoria_id: $request->categoria_id,
            nivel_id: $request->nivel_id,
            enunciado: $request->enunciado,
            imagen_url: $request->imagen_url,
            tiempo_limite_segundos: (int) $request->get('tiempo_limite_segundos', 20),
            activo: $request->boolean('activo', true),
            opciones: $request->input('opciones', []),
        ));

        return response()->json($dto, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->getPreguntaByIdHandler->handle(new GetTriviaPreguntaByIdQuery($id)));
    }

    public function update(UpdateTriviaPreguntaRequest $request, int $id): JsonResponse
    {
        return response()->json($this->updateHandler->handle(new UpdateTriviaPreguntaCommand(
            id: $id,
            data: $request->safe()->except(['opciones']),
            opciones: $request->has('opciones') ? $request->input('opciones') : null,
        )));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteTriviaPreguntaCommand($id));

        return response()->json(null, 204);
    }
}
