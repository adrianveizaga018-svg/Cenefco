<?php

namespace App\Http\Controllers\FormatosHojaSolicitud;

use App\Application\FormatosHojaSolicitud\Commands\CreateFormatoHojaSolicitudCommand;
use App\Application\FormatosHojaSolicitud\Commands\DeleteFormatoHojaSolicitudCommand;
use App\Application\FormatosHojaSolicitud\Commands\UpdateFormatoHojaSolicitudCommand;
use App\Application\FormatosHojaSolicitud\Handlers\CreateFormatoHojaSolicitudHandler;
use App\Application\FormatosHojaSolicitud\Handlers\DeleteFormatoHojaSolicitudHandler;
use App\Application\FormatosHojaSolicitud\Handlers\UpdateFormatoHojaSolicitudHandler;
use App\Application\FormatosHojaSolicitud\Queries\GetFormatoHojaSolicitudByIdQuery;
use App\Application\FormatosHojaSolicitud\Queries\GetFormatosHojaSolicitudQuery;
use App\Application\FormatosHojaSolicitud\QueryHandlers\GetFormatoHojaSolicitudByIdQueryHandler;
use App\Application\FormatosHojaSolicitud\QueryHandlers\GetFormatosHojaSolicitudQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\FormatosHojaSolicitud\StoreFormatoHojaSolicitudRequest;
use App\Http\Requests\FormatosHojaSolicitud\UpdateFormatoHojaSolicitudRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FormatoHojaSolicitudController extends Controller
{
    public function __construct(
        private readonly GetFormatosHojaSolicitudQueryHandler $listHandler,
        private readonly GetFormatoHojaSolicitudByIdQueryHandler $showHandler,
        private readonly CreateFormatoHojaSolicitudHandler $createHandler,
        private readonly UpdateFormatoHojaSolicitudHandler $updateHandler,
        private readonly DeleteFormatoHojaSolicitudHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 15),
        ]);

        return response()->json($this->listHandler->handle(new GetFormatosHojaSolicitudQuery(
            pagination:   $pagination,
            conInactivos: (bool) $request->get('conInactivos', false),
        )));
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->showHandler->handle(new GetFormatoHojaSolicitudByIdQuery($id)));
    }

    public function store(StoreFormatoHojaSolicitudRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateFormatoHojaSolicitudCommand(
            idFormatoHojaSolicitud: $request->integer('id_formato_hoja_solicitud'),
            nombre:                 $request->string('nombre')->toString(),
            descripcion:            $request->input('descripcion'),
            contenido:              $request->input('contenido'),
            idUsReg:                auth()->id(),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateFormatoHojaSolicitudRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateFormatoHojaSolicitudCommand(
            idFormatoHojaSolicitud: $id,
            nombre:                 $request->input('nombre'),
            descripcion:            $request->input('descripcion'),
            contenido:              $request->input('contenido'),
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteFormatoHojaSolicitudCommand($id));

        return response()->json(null, 204);
    }
}
