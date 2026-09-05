<?php

namespace App\Http\Controllers\Api;

use App\Application\Trivia\Commands\CreateTriviaPremioCommand;
use App\Application\Trivia\Commands\DeleteTriviaPremioCommand;
use App\Application\Trivia\Commands\UpdateTriviaPremioCommand;
use App\Application\Trivia\Handlers\CreateTriviaPremioHandler;
use App\Application\Trivia\Handlers\DeleteTriviaPremioHandler;
use App\Application\Trivia\Handlers\UpdateTriviaPremioHandler;
use App\Application\Trivia\Queries\GetTriviaPremiosActivosQuery;
use App\Application\Trivia\Queries\GetTriviaPremiosQuery;
use App\Application\Trivia\QueryHandlers\GetTriviaPremiosActivosQueryHandler;
use App\Application\Trivia\QueryHandlers\GetTriviaPremiosQueryHandler;
use App\Domain\Trivia\Contracts\TriviaPremioRepositoryInterface;
use App\Domain\Trivia\Exceptions\TriviaPremioNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Trivia\StoreTriviaPremioRequest;
use App\Http\Requests\Trivia\UpdateTriviaPremioRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TriviaPremioController extends Controller
{
    public function __construct(
        private readonly GetTriviaPremiosQueryHandler $getPremiosHandler,
        private readonly GetTriviaPremiosActivosQueryHandler $getPremiosActivosHandler,
        private readonly CreateTriviaPremioHandler $createHandler,
        private readonly UpdateTriviaPremioHandler $updateHandler,
        private readonly DeleteTriviaPremioHandler $deleteHandler,
        private readonly TriviaPremioRepositoryInterface $repository,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize' => $request->get('pageSize', 15),
            'query' => $request->get('query', ''),
        ]);

        return response()->json(
            $this->getPremiosHandler->handle(new GetTriviaPremiosQuery($pagination))
        );
    }

    public function indexPortal(): JsonResponse
    {
        return response()->json([
            'data' => $this->getPremiosActivosHandler->handle(new GetTriviaPremiosActivosQuery()),
        ]);
    }

    public function store(StoreTriviaPremioRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateTriviaPremioCommand(
            nombre: $request->nombre,
            descripcion: $request->descripcion,
            tipo: $request->tipo,
            imagen_url: $request->imagen_url,
            costo_puntos: (int) $request->costo_puntos,
            stock: $request->filled('stock') ? (int) $request->stock : null,
            activo: $request->boolean('activo', true),
            orden: (int) $request->get('orden', 0),
        ));

        return response()->json($dto, 201);
    }

    public function show(int $id): JsonResponse
    {
        $model = $this->repository->findById($id);
        if (! $model) {
            throw new TriviaPremioNotFoundException($id);
        }

        return response()->json(\App\Application\Trivia\DTOs\TriviaPremioDTO::fromModel($model));
    }

    public function update(UpdateTriviaPremioRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateTriviaPremioCommand(
            id: $id,
            nombre: $request->nombre,
            descripcion: $request->descripcion,
            tipo: $request->tipo,
            imagen_url: $request->imagen_url,
            costo_puntos: (int) $request->costo_puntos,
            stock: $request->filled('stock') ? (int) $request->stock : null,
            activo: $request->boolean('activo', true),
            orden: (int) $request->get('orden', 0),
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteTriviaPremioCommand($id));

        return response()->json(null, 204);
    }
}
