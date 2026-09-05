<?php

namespace App\Http\Controllers\Historiales;

use App\Application\Historiales\Commands\CreateHistorialCommand;
use App\Application\Historiales\Commands\DeleteHistorialCommand;
use App\Application\Historiales\Commands\UpdateHistorialCommand;
use App\Application\Historiales\Handlers\CreateHistorialHandler;
use App\Application\Historiales\Handlers\DeleteHistorialHandler;
use App\Application\Historiales\Handlers\UpdateHistorialHandler;
use App\Application\Historiales\Queries\GetHistorialByIdQuery;
use App\Application\Historiales\Queries\GetHistorialesQuery;
use App\Application\Historiales\QueryHandlers\GetHistorialByIdQueryHandler;
use App\Application\Historiales\QueryHandlers\GetHistorialesQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Historiales\StoreHistorialRequest;
use App\Http\Requests\Historiales\UpdateHistorialRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HistorialController extends Controller
{
    public function __construct(
        private readonly GetHistorialesQueryHandler $listHandler,
        private readonly GetHistorialByIdQueryHandler $showHandler,
        private readonly CreateHistorialHandler $createHandler,
        private readonly UpdateHistorialHandler $updateHandler,
        private readonly DeleteHistorialHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 15),
        ]);

        return response()->json($this->listHandler->handle(new GetHistorialesQuery(
            pagination:        $pagination,
            idUs:              $request->integer('id_us') ?: null,
            idTiporeferencia:  $request->integer('id_tiporeferencia') ?: null,
            idTipohistorial:   $request->integer('id_tipohistorial') ?: null,
            conInactivos:      (bool) $request->get('conInactivos', false),
        )));
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->showHandler->handle(new GetHistorialByIdQuery($id)));
    }

    public function store(StoreHistorialRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateHistorialCommand(
            idHistorial:      $request->integer('id_historial'),
            idUs:             $request->integer('id_us'),
            idTiporeferencia: $request->integer('id_tiporeferencia') ?: null,
            idTipohistorial:  $request->integer('id_tipohistorial') ?: null,
            descripcion:      $request->input('descripcion'),
            fecha:            $request->input('fecha'),
            idUsReg:          auth()->id(),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateHistorialRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateHistorialCommand(
            idHistorial:      $id,
            idUs:             $request->integer('id_us') ?: null,
            idTiporeferencia: $request->integer('id_tiporeferencia') ?: null,
            idTipohistorial:  $request->integer('id_tipohistorial') ?: null,
            descripcion:      $request->input('descripcion'),
            fecha:            $request->input('fecha'),
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteHistorialCommand($id));

        return response()->json(null, 204);
    }
}
