<?php

namespace App\Http\Controllers\Api;

use App\Application\TiposEvento\Commands\CreateTipoEventoCommand;
use App\Application\TiposEvento\Commands\DeleteTipoEventoCommand;
use App\Application\TiposEvento\Commands\UpdateTipoEventoCommand;
use App\Application\TiposEvento\Handlers\CreateTipoEventoHandler;
use App\Application\TiposEvento\Handlers\DeleteTipoEventoHandler;
use App\Application\TiposEvento\Handlers\UpdateTipoEventoHandler;
use App\Application\TiposEvento\Queries\GetTipoEventoByIdQuery;
use App\Application\TiposEvento\Queries\GetTiposEventoQuery;
use App\Application\TiposEvento\QueryHandlers\GetTipoEventoByIdQueryHandler;
use App\Application\TiposEvento\QueryHandlers\GetTiposEventoQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\TiposEvento\StoreTipoEventoRequest;
use App\Http\Requests\TiposEvento\UpdateTipoEventoRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TipoEventoController extends Controller
{
    public function __construct(
        private readonly GetTiposEventoQueryHandler $getTiposHandler,
        private readonly GetTipoEventoByIdQueryHandler $getTipoByIdHandler,
        private readonly CreateTipoEventoHandler $createHandler,
        private readonly UpdateTipoEventoHandler $updateHandler,
        private readonly DeleteTipoEventoHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize' => $request->get('pageSize', 100),
            'query' => $request->get('query', ''),
            'sortKey' => $request->input('sort.key', 'nombre'),
            'sortOrder' => $request->input('sort.order', 'asc'),
        ]);

        return response()->json(
            $this->getTiposHandler->handle(new GetTiposEventoQuery($pagination))
        );
    }

    public function store(StoreTipoEventoRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateTipoEventoCommand(
            nombre: $request->nombre,
            color_hex: $request->color_hex,
            activo: $request->boolean('activo', true),
        ));

        return response()->json($dto, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->getTipoByIdHandler->handle(new GetTipoEventoByIdQuery($id)));
    }

    public function update(UpdateTipoEventoRequest $request, int $id): JsonResponse
    {
        return response()->json($this->updateHandler->handle(new UpdateTipoEventoCommand($id, $request->validated())));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteTipoEventoCommand($id));

        return response()->json(null, 204);
    }
}
