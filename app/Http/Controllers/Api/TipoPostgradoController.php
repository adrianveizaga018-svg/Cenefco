<?php

namespace App\Http\Controllers\Api;

use App\Application\TiposPostgrado\Commands\CreateTipoPostgradoCommand;
use App\Application\TiposPostgrado\Commands\DeleteTipoPostgradoCommand;
use App\Application\TiposPostgrado\Commands\UpdateTipoPostgradoCommand;
use App\Application\TiposPostgrado\Handlers\CreateTipoPostgradoHandler;
use App\Application\TiposPostgrado\Handlers\DeleteTipoPostgradoHandler;
use App\Application\TiposPostgrado\Handlers\UpdateTipoPostgradoHandler;
use App\Application\TiposPostgrado\Queries\GetTipoPostgradoByIdQuery;
use App\Application\TiposPostgrado\Queries\GetTiposPostgradoQuery;
use App\Application\TiposPostgrado\QueryHandlers\GetTipoPostgradoByIdQueryHandler;
use App\Application\TiposPostgrado\QueryHandlers\GetTiposPostgradoQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\TiposPostgrado\StoreTipoPostgradoRequest;
use App\Http\Requests\TiposPostgrado\UpdateTipoPostgradoRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TipoPostgradoController extends Controller
{
    public function __construct(
        private readonly GetTiposPostgradoQueryHandler $listHandler,
        private readonly GetTipoPostgradoByIdQueryHandler $showHandler,
        private readonly CreateTipoPostgradoHandler $createHandler,
        private readonly UpdateTipoPostgradoHandler $updateHandler,
        private readonly DeleteTipoPostgradoHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->listHandler->handle(new GetTiposPostgradoQuery(
            pageIndex:   (int) $request->get('pageIndex', 1),
            pageSize:    (int) $request->get('pageSize', 50),
            idPlan:      $request->integer('id_plan') ?: null,
            idTipopago:  $request->integer('id_tipopago') ?: null,
            conInactivos: $request->boolean('conInactivos', false),
        )));
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->showHandler->handle(new GetTipoPostgradoByIdQuery($id)));
    }

    public function store(StoreTipoPostgradoRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateTipoPostgradoCommand(
            idTipopost:         $request->integer('id_tipopost'),
            idPlan:             $request->integer('id_plan'),
            idUsReg:            $request->integer('id_us_reg') ?: auth()->id(),
            numTipopost:        $request->integer('num_tipopost', 0),
            idTipopago:         $request->integer('id_tipopago') ?: null,
            descuentopostgrado: $request->input('descuentopostgrado'),
            calculoCuota:       $request->input('calculo_cuota'),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateTipoPostgradoRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateTipoPostgradoCommand(
            idTipopost:         $id,
            idTipopago:         $request->integer('id_tipopago') ?: null,
            descuentopostgrado: $request->input('descuentopostgrado'),
            calculoCuota:       $request->input('calculo_cuota'),
            estado:             $request->integer('estado') ?: null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteTipoPostgradoCommand($id));

        return response()->json(null, 204);
    }
}
