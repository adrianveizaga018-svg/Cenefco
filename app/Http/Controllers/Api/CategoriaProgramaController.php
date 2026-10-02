<?php

namespace App\Http\Controllers\Api;

use App\Application\CategoriasPrograma\Commands\CreateCategoriaProgramaCommand;
use App\Application\CategoriasPrograma\Commands\DeleteCategoriaProgramaCommand;
use App\Application\CategoriasPrograma\Commands\UpdateCategoriaProgramaCommand;
use App\Application\CategoriasPrograma\Handlers\CreateCategoriaProgramaHandler;
use App\Application\CategoriasPrograma\Handlers\DeleteCategoriaProgramaHandler;
use App\Application\CategoriasPrograma\Handlers\UpdateCategoriaProgramaHandler;
use App\Application\CategoriasPrograma\Queries\GetCategoriaProgramaByIdQuery;
use App\Application\CategoriasPrograma\Queries\GetCategoriasProgramaQuery;
use App\Application\CategoriasPrograma\QueryHandlers\GetCategoriaProgramaByIdQueryHandler;
use App\Application\CategoriasPrograma\QueryHandlers\GetCategoriasProgramaQueryHandler;
use App\Domain\CategoriasPrograma\Contracts\CategoriaProgramaRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoriasPrograma\StoreCategoriaProgramaRequest;
use App\Http\Requests\CategoriasPrograma\UpdateCategoriaProgramaRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoriaProgramaController extends Controller
{
    public function __construct(
        private readonly GetCategoriasProgramaQueryHandler $getCategoriasHandler,
        private readonly GetCategoriaProgramaByIdQueryHandler $getByIdHandler,
        private readonly CreateCategoriaProgramaHandler $createHandler,
        private readonly UpdateCategoriaProgramaHandler $updateHandler,
        private readonly DeleteCategoriaProgramaHandler $deleteHandler,
        private readonly CategoriaProgramaRepositoryInterface $repository,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 100),
            'query'     => $request->get('query', ''),
            'sortKey'   => 'orden',
            'sortOrder' => 'asc',
        ]);

        return response()->json(
            $this->getCategoriasHandler->handle(
                new GetCategoriasProgramaQuery($pagination, $request->boolean('soloActivos', false))
            )
        );
    }

    public function tiposLegacy(): JsonResponse
    {
        return response()->json($this->repository->tiposLegacy());
    }

    public function indexComoTipos(): JsonResponse
    {
        $data = $this->repository->indexComoTipos();

        return response()->json(['data' => $data, 'total' => count($data)]);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->getByIdHandler->handle(new GetCategoriaProgramaByIdQuery($id)));
    }

    public function store(StoreCategoriaProgramaRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateCategoriaProgramaCommand(
            nombre:           $request->nombre,
            slug:             $request->slug,
            descripcion:      $request->descripcion,
            icono:            $request->icono,
            color:            $request->color,
            orden:            $request->integer('orden', 0),
            activo:           $request->boolean('activo', true),
            meta_titulo:      $request->meta_titulo,
            meta_descripcion: $request->meta_descripcion,
            tipo_programa_id: $request->tipo_programa_id ? (int) $request->tipo_programa_id : null,
            comision_monto:   (float) $request->comision_monto,
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateCategoriaProgramaRequest $request, int $id): JsonResponse
    {
        return response()->json($this->updateHandler->handle(new UpdateCategoriaProgramaCommand(
            id:               $id,
            nombre:           $request->nombre,
            descripcion:      $request->descripcion,
            icono:            $request->icono,
            color:            $request->color,
            orden:            $request->has('orden') ? $request->integer('orden') : null,
            activo:           $request->has('activo') ? $request->boolean('activo') : null,
            meta_titulo:      $request->meta_titulo,
            meta_descripcion: $request->meta_descripcion,
            tipo_programa_id: $request->filled('tipo_programa_id') ? (int) $request->tipo_programa_id : null,
            comision_monto:   $request->has('comision_monto') ? (float) $request->comision_monto : null,
        )));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteCategoriaProgramaCommand($id));

        return response()->json(null, 204);
    }
}
