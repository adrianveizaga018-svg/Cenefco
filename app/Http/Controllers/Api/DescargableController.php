<?php

namespace App\Http\Controllers\Api;

use App\Application\Descargables\Commands\CreateDescargableCommand;
use App\Application\Descargables\Commands\DeleteDescargableCommand;
use App\Application\Descargables\Commands\UpdateDescargableCommand;
use App\Application\Descargables\Handlers\CreateDescargableHandler;
use App\Application\Descargables\Handlers\DeleteDescargableHandler;
use App\Application\Descargables\Handlers\UpdateDescargableHandler;
use App\Application\Descargables\Queries\GetDescargableByIdQuery;
use App\Application\Descargables\Queries\GetDescargablesQuery;
use App\Application\Descargables\QueryHandlers\GetDescargableByIdQueryHandler;
use App\Application\Descargables\QueryHandlers\GetDescargablesQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Descargables\StoreDescargableRequest;
use App\Http\Requests\Descargables\UpdateDescargableRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DescargableController extends Controller
{
    public function __construct(
        private readonly GetDescargablesQueryHandler $getDescargablesHandler,
        private readonly GetDescargableByIdQueryHandler $getByIdHandler,
        private readonly CreateDescargableHandler $createHandler,
        private readonly UpdateDescargableHandler $updateHandler,
        private readonly DeleteDescargableHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 20),
            'query'     => $request->get('query', ''),
            'sortKey'   => $request->input('sort.key', 'orden'),
            'sortOrder' => $request->input('sort.order', 'asc'),
        ]);

        return response()->json(
            $this->getDescargablesHandler->handle(
                new GetDescargablesQuery($pagination, $request->boolean('soloActivos', false))
            )
        );
    }

    public function store(StoreDescargableRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateDescargableCommand(
            nombre:              $request->nombre,
            archivo_url:         $request->archivo_url,
            tipo:                $request->tipo,
            imagen_portada_url:  $request->imagen_portada_url,
            programa_id:         $request->programa_id ? (int) $request->programa_id : null,
            requiere_datos:      $request->boolean('requiere_datos', true),
            orden:               $request->integer('orden', 0),
            activo:              $request->boolean('activo', true),
        ));

        return response()->json($dto, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->getByIdHandler->handle(new GetDescargableByIdQuery($id)));
    }

    public function update(UpdateDescargableRequest $request, int $id): JsonResponse
    {
        return response()->json($this->updateHandler->handle(new UpdateDescargableCommand(
            id:                 $id,
            nombre:             $request->nombre,
            archivo_url:        $request->archivo_url,
            tipo:               $request->tipo,
            imagen_portada_url: $request->imagen_portada_url,
            programa_id:        $request->has('programa_id') ? (int) $request->programa_id : null,
            requiere_datos:     $request->has('requiere_datos') ? $request->boolean('requiere_datos') : null,
            orden:              $request->has('orden') ? $request->integer('orden') : null,
            activo:             $request->has('activo') ? $request->boolean('activo') : null,
        )));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteDescargableCommand($id));

        return response()->json(null, 204);
    }
}
