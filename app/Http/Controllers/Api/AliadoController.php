<?php

namespace App\Http\Controllers\Api;

use App\Application\Aliados\Commands\CreateAliadoCommand;
use App\Application\Aliados\Commands\DeleteAliadoCommand;
use App\Application\Aliados\Commands\UpdateAliadoCommand;
use App\Application\Aliados\Handlers\CreateAliadoHandler;
use App\Application\Aliados\Handlers\DeleteAliadoHandler;
use App\Application\Aliados\Handlers\UpdateAliadoHandler;
use App\Application\Aliados\Queries\GetAliadoByIdQuery;
use App\Application\Aliados\Queries\GetAliadosQuery;
use App\Application\Aliados\QueryHandlers\GetAliadoByIdQueryHandler;
use App\Application\Aliados\QueryHandlers\GetAliadosQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Aliados\StoreAliadoRequest;
use App\Http\Requests\Aliados\UpdateAliadoRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AliadoController extends Controller
{
    public function __construct(
        private readonly GetAliadosQueryHandler $getAliadosHandler,
        private readonly GetAliadoByIdQueryHandler $getByIdHandler,
        private readonly CreateAliadoHandler $createHandler,
        private readonly UpdateAliadoHandler $updateHandler,
        private readonly DeleteAliadoHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 50),
            'query'     => $request->get('query', ''),
            'sortKey'   => $request->input('sort.key', 'orden'),
            'sortOrder' => $request->input('sort.order', 'asc'),
        ]);

        return response()->json(
            $this->getAliadosHandler->handle(
                new GetAliadosQuery($pagination, $request->boolean('soloActivos', false))
            )
        );
    }

    public function store(StoreAliadoRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateAliadoCommand(
            nombre:      $request->nombre,
            logo_url:    $request->logo_url,
            logo_alt:    $request->logo_alt,
            url_sitio:   $request->url_sitio,
            descripcion: $request->descripcion,
            tipo:        $request->tipo,
            orden:       $request->integer('orden', 0),
            activo:      $request->boolean('activo', true),
        ));

        return response()->json($dto, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->getByIdHandler->handle(new GetAliadoByIdQuery($id)));
    }

    public function update(UpdateAliadoRequest $request, int $id): JsonResponse
    {
        return response()->json($this->updateHandler->handle(new UpdateAliadoCommand(
            id:          $id,
            nombre:      $request->nombre,
            logo_url:    $request->logo_url,
            logo_alt:    $request->logo_alt,
            url_sitio:   $request->url_sitio,
            descripcion: $request->descripcion,
            tipo:        $request->tipo,
            orden:       $request->has('orden') ? $request->integer('orden') : null,
            activo:      $request->has('activo') ? $request->boolean('activo') : null,
        )));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteAliadoCommand($id));

        return response()->json(null, 204);
    }
}
