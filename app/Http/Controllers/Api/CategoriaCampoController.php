<?php

namespace App\Http\Controllers\Api;

use App\Application\CategoriasCampo\Commands\CreateCategoriaCampoCommand;
use App\Application\CategoriasCampo\Commands\DeleteCategoriaCampoCommand;
use App\Application\CategoriasCampo\Commands\ReorderCategoriaCampoCommand;
use App\Application\CategoriasCampo\Commands\UpdateCategoriaCampoCommand;
use App\Application\CategoriasCampo\Handlers\CreateCategoriaCampoHandler;
use App\Application\CategoriasCampo\Handlers\DeleteCategoriaCampoHandler;
use App\Application\CategoriasCampo\Handlers\ReorderCategoriaCampoHandler;
use App\Application\CategoriasCampo\Handlers\UpdateCategoriaCampoHandler;
use App\Application\CategoriasCampo\Queries\GetCamposByCategoriaQuery;
use App\Application\CategoriasCampo\Queries\GetCategoriaCampoByIdQuery;
use App\Application\CategoriasCampo\QueryHandlers\GetCamposByCategoriaQueryHandler;
use App\Application\CategoriasCampo\QueryHandlers\GetCategoriaCampoByIdQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoriasCampo\ReorderCategoriaCampoRequest;
use App\Http\Requests\CategoriasCampo\StoreCategoriaCampoRequest;
use App\Http\Requests\CategoriasCampo\UpdateCategoriaCampoRequest;
use Illuminate\Http\JsonResponse;

class CategoriaCampoController extends Controller
{
    public function __construct(
        private readonly GetCamposByCategoriaQueryHandler $getCamposHandler,
        private readonly GetCategoriaCampoByIdQueryHandler $getByIdHandler,
        private readonly CreateCategoriaCampoHandler $createHandler,
        private readonly UpdateCategoriaCampoHandler $updateHandler,
        private readonly DeleteCategoriaCampoHandler $deleteHandler,
        private readonly ReorderCategoriaCampoHandler $reorderHandler,
    ) {}

    public function index(int $categoriaId): JsonResponse
    {
        $data = $this->getCamposHandler->handle(new GetCamposByCategoriaQuery($categoriaId));

        return response()->json(['data' => $data, 'total' => count($data)]);
    }

    public function store(StoreCategoriaCampoRequest $request, int $categoriaId): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateCategoriaCampoCommand(
            categoria_id: $categoriaId,
            nombre_campo: $request->nombre_campo,
            etiqueta:     $request->etiqueta,
            tipo_campo:   $request->input('tipo_campo', 'text'),
            requerido:    $request->boolean('requerido', false),
            orden:        $request->integer('orden', 0),
            paso:         $request->integer('paso', 2),
            activo:       $request->boolean('activo', true),
            ayuda:        $request->ayuda,
            opciones:     $request->opciones,
            validacion:   $request->validacion,
        ));

        return response()->json($dto, 201);
    }

    public function show(int $categoriaId, int $id): JsonResponse
    {
        return response()->json($this->getByIdHandler->handle(new GetCategoriaCampoByIdQuery($categoriaId, $id)));
    }

    public function update(UpdateCategoriaCampoRequest $request, int $categoriaId, int $id): JsonResponse
    {
        return response()->json($this->updateHandler->handle(new UpdateCategoriaCampoCommand(
            id:         $id,
            etiqueta:   $request->etiqueta,
            tipo_campo: $request->tipo_campo,
            requerido:  $request->has('requerido') ? $request->boolean('requerido') : null,
            orden:      $request->has('orden') ? $request->integer('orden') : null,
            activo:     $request->has('activo') ? $request->boolean('activo') : null,
            ayuda:      $request->ayuda,
            opciones:   $request->has('opciones') ? $request->opciones : null,
            validacion: $request->has('validacion') ? $request->validacion : null,
        )));
    }

    public function destroy(int $categoriaId, int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteCategoriaCampoCommand($categoriaId, $id));

        return response()->json(null, 204);
    }

    public function reorder(ReorderCategoriaCampoRequest $request, int $categoriaId): JsonResponse
    {
        $this->reorderHandler->handle(new ReorderCategoriaCampoCommand(
            categoria_id: $categoriaId,
            items:        $request->items,
        ));

        return response()->json(['ok' => true]);
    }
}
