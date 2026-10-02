<?php

namespace App\Http\Controllers\Api;

use App\Application\TiposPrograma\Commands\CreateTipoProgramaCommand;
use App\Application\TiposPrograma\Commands\DeleteTipoProgramaCommand;
use App\Application\TiposPrograma\Commands\UpdateTipoProgramaCommand;
use App\Application\TiposPrograma\Handlers\CreateTipoProgramaHandler;
use App\Application\TiposPrograma\Handlers\DeleteTipoProgramaHandler;
use App\Application\TiposPrograma\Handlers\UpdateTipoProgramaHandler;
use App\Application\TiposPrograma\Queries\GetTipoProgramaByIdQuery;
use App\Application\TiposPrograma\Queries\GetTiposProgramaQuery;
use App\Application\TiposPrograma\QueryHandlers\GetTipoProgramaByIdQueryHandler;
use App\Application\TiposPrograma\QueryHandlers\GetTiposProgramaQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\TiposPrograma\StoreTipoProgramaRequest;
use App\Http\Requests\TiposPrograma\UpdateTipoProgramaRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TipoProgramaController extends Controller
{
    public function __construct(
        private readonly GetTiposProgramaQueryHandler $listHandler,
        private readonly GetTipoProgramaByIdQueryHandler $showHandler,
        private readonly CreateTipoProgramaHandler $createHandler,
        private readonly UpdateTipoProgramaHandler $updateHandler,
        private readonly DeleteTipoProgramaHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->listHandler->handle(new GetTiposProgramaQuery(
            pageIndex:   (int) $request->get('pageIndex', 1),
            pageSize:    (int) $request->get('pageSize', 50),
            query:       $request->input('query'),
            conInactivos: $request->boolean('conInactivos', false),
        )));
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->showHandler->handle(new GetTipoProgramaByIdQuery($id)));
    }

    public function store(StoreTipoProgramaRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateTipoProgramaCommand(
            idTipoprograma:     $request->integer('id_tipoprograma'),
            nombreTipoprograma: $request->string('nombre_tipoprograma')->toString(),
            idUsReg:            auth()->id(),
            estado:             $request->integer('estado') ?: null,
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateTipoProgramaRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateTipoProgramaCommand(
            idTipoprograma:     $id,
            nombreTipoprograma: $request->input('nombre_tipoprograma'),
            estado:             $request->integer('estado') ?: null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteTipoProgramaCommand($id));

        return response()->json(null, 204);
    }
}
