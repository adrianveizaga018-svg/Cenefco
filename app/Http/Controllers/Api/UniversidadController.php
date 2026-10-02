<?php

namespace App\Http\Controllers\Api;

use App\Application\Universidades\Commands\CreateUniversidadCommand;
use App\Application\Universidades\Commands\DeleteUniversidadCommand;
use App\Application\Universidades\Commands\UpdateUniversidadCommand;
use App\Application\Universidades\Handlers\CreateUniversidadHandler;
use App\Application\Universidades\Handlers\DeleteUniversidadHandler;
use App\Application\Universidades\Handlers\UpdateUniversidadHandler;
use App\Application\Universidades\Queries\GetUniversidadByIdQuery;
use App\Application\Universidades\Queries\GetUniversidadesQuery;
use App\Application\Universidades\QueryHandlers\GetUniversidadByIdQueryHandler;
use App\Application\Universidades\QueryHandlers\GetUniversidadesQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Universidades\StoreUniversidadRequest;
use App\Http\Requests\Universidades\UpdateUniversidadRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UniversidadController extends Controller
{
    public function __construct(
        private readonly GetUniversidadesQueryHandler   $getUniversidadesHandler,
        private readonly GetUniversidadByIdQueryHandler $getByIdHandler,
        private readonly CreateUniversidadHandler       $createHandler,
        private readonly UpdateUniversidadHandler       $updateHandler,
        private readonly DeleteUniversidadHandler       $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->integer('pageIndex', 1),
            'pageSize'  => $request->integer('pageSize', 20),
        ]);

        return response()->json(
            $this->getUniversidadesHandler->handle(new GetUniversidadesQuery(
                pagination:        $pagination,
                query:             $request->input('query'),
                idCiudad:          $request->integer('idCiudad') ?: null,
                idTipoUniversidad: $request->integer('idTipoUniversidad') ?: null,
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->getByIdHandler->handle(new GetUniversidadByIdQuery($id))
        );
    }

    public function store(StoreUniversidadRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateUniversidadCommand(
            nombreUniversidad: $request->input('nombre_universidad'),
            idCiudad:          $request->integer('id_ciudad'),
            idTipouniversidad: $request->integer('id_tipouniversidad'),
            idUsReg:           $request->integer('id_us_reg'),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateUniversidadRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateUniversidadCommand(
            id:                $id,
            nombreUniversidad: $request->input('nombre_universidad'),
            idCiudad:          $request->has('id_ciudad') ? $request->integer('id_ciudad') : null,
            idTipouniversidad: $request->has('id_tipouniversidad') ? $request->integer('id_tipouniversidad') : null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteUniversidadCommand($id));
        return response()->json(null, 204);
    }
}
