<?php

namespace App\Http\Controllers\Api;

use App\Application\Gastos\Commands\CreateCategoriaGastoCommand;
use App\Application\Gastos\Handlers\CreateCategoriaGastoHandler;
use App\Application\Gastos\Queries\GetCategoriasGastoQuery;
use App\Application\Gastos\QueryHandlers\GetCategoriasGastoQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Gastos\StoreCategoriaGastoRequest;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Gastos')]
class CategoriaGastoController extends Controller
{
    public function __construct(
        private readonly GetCategoriasGastoQueryHandler $getListHandler,
        private readonly CreateCategoriaGastoHandler     $createHandler,
    ) {}

    #[OA\Get(path: '/api/v1/categorias-gasto', summary: 'Listar categorías de gasto activas', tags: ['Gastos'])]
    public function index(): JsonResponse
    {
        return response()->json($this->getListHandler->handle(new GetCategoriasGastoQuery()));
    }

    #[OA\Post(path: '/api/v1/categorias-gasto', summary: 'Crear categoría de gasto', tags: ['Gastos'])]
    public function store(StoreCategoriaGastoRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateCategoriaGastoCommand(
            nombre:        $request->nombre,
            linea_negocio: $request->linea_negocio,
            activo:        $request->boolean('activo', true),
        ));

        return response()->json($dto, 201);
    }
}
