<?php

namespace App\Http\Controllers\Api;

use App\Application\Expedidos\Commands\CreateExpedidoCommand;
use App\Application\Expedidos\Commands\DeleteExpedidoCommand;
use App\Application\Expedidos\Commands\UpdateExpedidoCommand;
use App\Application\Expedidos\Handlers\CreateExpedidoHandler;
use App\Application\Expedidos\Handlers\DeleteExpedidoHandler;
use App\Application\Expedidos\Handlers\UpdateExpedidoHandler;
use App\Application\Expedidos\Queries\GetExpedidoByIdQuery;
use App\Application\Expedidos\Queries\GetExpedidosQuery;
use App\Application\Expedidos\QueryHandlers\GetExpedidoByIdQueryHandler;
use App\Application\Expedidos\QueryHandlers\GetExpedidosQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Expedidos\StoreExpedidoRequest;
use App\Http\Requests\Expedidos\UpdateExpedidoRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpedidoController extends Controller
{
    public function __construct(
        private readonly GetExpedidosQueryHandler    $getExpedidosHandler,
        private readonly GetExpedidoByIdQueryHandler $getByIdHandler,
        private readonly CreateExpedidoHandler       $createHandler,
        private readonly UpdateExpedidoHandler       $updateHandler,
        private readonly DeleteExpedidoHandler       $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->integer('pageIndex', 1),
            'pageSize'  => $request->integer('pageSize', 50),
        ]);

        return response()->json(
            $this->getExpedidosHandler->handle(new GetExpedidosQuery(
                pagination: $pagination,
                query:      $request->input('query'),
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->getByIdHandler->handle(new GetExpedidoByIdQuery($id))
        );
    }

    public function store(StoreExpedidoRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateExpedidoCommand(
            nombre: $request->input('nombre'),
            orden:  $request->integer('orden', 0),
            activo: $request->boolean('activo', true),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateExpedidoRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateExpedidoCommand(
            id:     $id,
            nombre: $request->input('nombre'),
            orden:  $request->has('orden') ? $request->integer('orden') : null,
            activo: $request->has('activo') ? $request->boolean('activo') : null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteExpedidoCommand($id));
        return response()->json(null, 204);
    }
}
