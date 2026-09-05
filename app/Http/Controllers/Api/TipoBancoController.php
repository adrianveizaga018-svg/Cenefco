<?php

namespace App\Http\Controllers\Api;

use App\Application\TiposBanco\Commands\CreateTipoBancoCommand;
use App\Application\TiposBanco\Commands\DeleteTipoBancoCommand;
use App\Application\TiposBanco\Commands\UpdateTipoBancoCommand;
use App\Application\TiposBanco\Handlers\CreateTipoBancoHandler;
use App\Application\TiposBanco\Handlers\DeleteTipoBancoHandler;
use App\Application\TiposBanco\Handlers\UpdateTipoBancoHandler;
use App\Application\TiposBanco\Queries\GetTipoBancoByIdQuery;
use App\Application\TiposBanco\Queries\GetTiposBancoActivosQuery;
use App\Application\TiposBanco\Queries\GetTiposBancoQuery;
use App\Application\TiposBanco\QueryHandlers\GetTipoBancoByIdQueryHandler;
use App\Application\TiposBanco\QueryHandlers\GetTiposBancoActivosQueryHandler;
use App\Application\TiposBanco\QueryHandlers\GetTiposBancoQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\TiposBanco\StoreTipoBancoRequest;
use App\Http\Requests\TiposBanco\UpdateTipoBancoRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TipoBancoController extends Controller
{
    public function __construct(
        private readonly GetTiposBancoQueryHandler $getListHandler,
        private readonly GetTiposBancoActivosQueryHandler $getActivosHandler,
        private readonly GetTipoBancoByIdQueryHandler $getByIdHandler,
        private readonly CreateTipoBancoHandler     $createHandler,
        private readonly UpdateTipoBancoHandler     $updateHandler,
        private readonly DeleteTipoBancoHandler     $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 50),
            'query'     => $request->get('query', ''),
            'sortKey'   => 'orden',
            'sortOrder' => 'asc',
        ], 'orden');

        return response()->json($this->getListHandler->handle(new GetTiposBancoQuery($pagination)));
    }

    public function activos(): JsonResponse
    {
        return response()->json($this->getActivosHandler->handle(new GetTiposBancoActivosQuery()));
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->getByIdHandler->handle(new GetTipoBancoByIdQuery($id)));
    }

    public function store(StoreTipoBancoRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateTipoBancoCommand(
            nombre: $request->nombre,
            activo: $request->boolean('activo', true),
            orden:  $request->integer('orden', 0),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateTipoBancoRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateTipoBancoCommand(
            id:     $id,
            nombre: $request->nombre,
            activo: $request->has('activo') ? $request->boolean('activo') : null,
            orden:  $request->filled('orden') ? $request->integer('orden') : null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteTipoBancoCommand($id));

        return response()->json(null, 204);
    }
}
