<?php

namespace App\Http\Controllers\Api;

use App\Application\FechasDoc\Commands\CreateFechaDocCommand;
use App\Application\FechasDoc\Commands\DeleteFechaDocCommand;
use App\Application\FechasDoc\Commands\UpdateFechaDocCommand;
use App\Application\FechasDoc\Handlers\CreateFechaDocHandler;
use App\Application\FechasDoc\Handlers\DeleteFechaDocHandler;
use App\Application\FechasDoc\Handlers\UpdateFechaDocHandler;
use App\Application\FechasDoc\Queries\GetFechaDocByIdQuery;
use App\Application\FechasDoc\Queries\GetFechasDocQuery;
use App\Application\FechasDoc\QueryHandlers\GetFechaDocByIdQueryHandler;
use App\Application\FechasDoc\QueryHandlers\GetFechasDocQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\FechasDoc\StoreFechaDocRequest;
use App\Http\Requests\FechasDoc\UpdateFechaDocRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FechaDocController extends Controller
{
    public function __construct(
        private readonly GetFechasDocQueryHandler $getFechasDocHandler,
        private readonly GetFechaDocByIdQueryHandler $getFechaDocByIdHandler,
        private readonly CreateFechaDocHandler $createHandler,
        private readonly UpdateFechaDocHandler $updateHandler,
        private readonly DeleteFechaDocHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 50),
            'query'     => '',
            'sortKey'   => 'fecha_inicio',
            'sortOrder' => 'asc',
        ]);

        return response()->json(
            $this->getFechasDocHandler->handle(new GetFechasDocQuery(
                pagination:   $pagination,
                conInactivos: $request->boolean('conInactivos', false),
                idPlandoc:    $request->has('id_plandoc') ? $request->integer('id_plandoc') : null,
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->getFechaDocByIdHandler->handle(new GetFechaDocByIdQuery($id))
        );
    }

    public function store(StoreFechaDocRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateFechaDocCommand(
            id_fechadoc:    $request->integer('id_fechadoc'),
            id_plandoc:     $request->integer('id_plandoc'),
            id_us_reg:      $request->integer('id_us_reg', 0),
            num_fechadoc:   $request->integer('num_fechadoc', 0),
            nro_doc:        $request->nro_doc,
            tipo_documento: $request->tipo_documento,
            fecha_inicio:   $request->fecha_inicio,
            fecha_fin:      $request->fecha_fin,
            obligatorio:    $request->filled('obligatorio') ? $request->integer('obligatorio') : null,
            estado:         $request->integer('estado', 1),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateFechaDocRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateFechaDocCommand(
            id:             $id,
            nro_doc:        $request->nro_doc,
            tipo_documento: $request->tipo_documento,
            fecha_inicio:   $request->fecha_inicio,
            fecha_fin:      $request->fecha_fin,
            obligatorio:    $request->filled('obligatorio') ? $request->integer('obligatorio') : null,
            estado:         $request->filled('estado') ? $request->integer('estado') : null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteFechaDocCommand($id));

        return response()->json(null, 204);
    }
}
