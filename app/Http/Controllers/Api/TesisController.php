<?php

namespace App\Http\Controllers\Api;

use App\Application\Tesis\Commands\CreateTesisCommand;
use App\Application\Tesis\Commands\DeleteTesisCommand;
use App\Application\Tesis\Commands\UpdateTesisCommand;
use App\Application\Tesis\Handlers\CreateTesisHandler;
use App\Application\Tesis\Handlers\DeleteTesisHandler;
use App\Application\Tesis\Handlers\UpdateTesisHandler;
use App\Application\Tesis\Queries\GetTesisByIdQuery;
use App\Application\Tesis\Queries\GetTesisQuery;
use App\Application\Tesis\QueryHandlers\GetTesisByIdQueryHandler;
use App\Application\Tesis\QueryHandlers\GetTesisQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tesis\StoreTesisRequest;
use App\Http\Requests\Tesis\UpdateTesisRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TesisController extends Controller
{
    public function __construct(
        private readonly GetTesisQueryHandler    $listHandler,
        private readonly GetTesisByIdQueryHandler $showHandler,
        private readonly CreateTesisHandler       $createHandler,
        private readonly UpdateTesisHandler       $updateHandler,
        private readonly DeleteTesisHandler       $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 30),
        ]);

        return response()->json(
            $this->listHandler->handle(new GetTesisQuery(
                pagination:   $pagination,
                query:        $request->get('query') ?: null,
                tipoTesis:    $request->has('tipo_tesis') ? (int) $request->get('tipo_tesis') : null,
                conInactivos: $request->boolean('conInactivos', false),
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->showHandler->handle(new GetTesisByIdQuery($id))
        );
    }

    public function store(StoreTesisRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateTesisCommand(
            id_tesis:          $request->integer('id_tesis'),
            id_us_reg:         $request->integer('id_us_reg', 0),
            num_tesis:         $request->integer('num_tesis', 0),
            titulo_tesis:      $request->input('titulo_tesis'),
            descripcion_tesis: $request->input('descripcion_tesis'),
            fecha_publicacion: $request->input('fecha_publicacion'),
            autor:             $request->input('autor'),
            tipo_tesis:        $request->has('tipo_tesis') ? $request->integer('tipo_tesis') : null,
            archivo:           $request->input('archivo'),
            estado:            $request->integer('estado', 1),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateTesisRequest $request, int $id): JsonResponse
    {
        return response()->json(
            $this->updateHandler->handle(new UpdateTesisCommand(
                id:                $id,
                titulo_tesis:      $request->input('titulo_tesis'),
                descripcion_tesis: $request->input('descripcion_tesis'),
                fecha_publicacion: $request->input('fecha_publicacion'),
                autor:             $request->input('autor'),
                tipo_tesis:        $request->has('tipo_tesis') ? $request->integer('tipo_tesis') : null,
                archivo:           $request->input('archivo'),
                estado:            $request->has('estado') ? $request->integer('estado') : null,
            ))
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteTesisCommand($id));

        return response()->json(null, 204);
    }
}
