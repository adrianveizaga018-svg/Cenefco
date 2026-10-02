<?php

namespace App\Http\Controllers\Api;

use App\Application\GaleriasCategorias\Commands\CreateGaleriaCategoriaCommand;
use App\Application\GaleriasCategorias\Commands\DeleteGaleriaCategoriaCommand;
use App\Application\GaleriasCategorias\Commands\UpdateGaleriaCategoriaCommand;
use App\Application\GaleriasCategorias\Handlers\CreateGaleriaCategoriaHandler;
use App\Application\GaleriasCategorias\Handlers\DeleteGaleriaCategoriaHandler;
use App\Application\GaleriasCategorias\Handlers\UpdateGaleriaCategoriaHandler;
use App\Application\GaleriasCategorias\Queries\GetGaleriaCategoriaByIdQuery;
use App\Application\GaleriasCategorias\Queries\GetGaleriasCategoriasQuery;
use App\Application\GaleriasCategorias\QueryHandlers\GetGaleriaCategoriaByIdQueryHandler;
use App\Application\GaleriasCategorias\QueryHandlers\GetGaleriasCategoriasQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\GaleriasCategorias\StoreGaleriaCategoriaRequest;
use App\Http\Requests\GaleriasCategorias\UpdateGaleriaCategoriaRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GaleriaCategoriaController extends Controller
{
    public function __construct(
        private readonly GetGaleriasCategoriasQueryHandler $getCategoriasHandler,
        private readonly GetGaleriaCategoriaByIdQueryHandler $getByIdHandler,
        private readonly CreateGaleriaCategoriaHandler $createHandler,
        private readonly UpdateGaleriaCategoriaHandler $updateHandler,
        private readonly DeleteGaleriaCategoriaHandler $deleteHandler,
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
            $this->getCategoriasHandler->handle(
                new GetGaleriasCategoriasQuery($pagination, $request->boolean('soloActivos', false))
            )
        );
    }

    public function store(StoreGaleriaCategoriaRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateGaleriaCategoriaCommand(
            nombre:      $request->nombre,
            descripcion: $request->descripcion,
            orden:       $request->integer('orden', 0),
            activo:      $request->boolean('activo', true),
        ));

        return response()->json($dto, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->getByIdHandler->handle(new GetGaleriaCategoriaByIdQuery($id)));
    }

    public function update(UpdateGaleriaCategoriaRequest $request, int $id): JsonResponse
    {
        return response()->json($this->updateHandler->handle(new UpdateGaleriaCategoriaCommand(
            id:          $id,
            nombre:      $request->nombre,
            descripcion: $request->descripcion,
            orden:       $request->has('orden') ? $request->integer('orden') : null,
            activo:      $request->has('activo') ? $request->boolean('activo') : null,
        )));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteGaleriaCategoriaCommand($id));

        return response()->json(null, 204);
    }
}
