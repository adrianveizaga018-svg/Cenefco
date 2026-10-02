<?php

namespace App\Http\Controllers\Api;

use App\Application\CategoriasNoticia\Commands\CreateCategoriaNoticiaCommand;
use App\Application\CategoriasNoticia\Commands\DeleteCategoriaNoticiaCommand;
use App\Application\CategoriasNoticia\Commands\UpdateCategoriaNoticiaCommand;
use App\Application\CategoriasNoticia\Handlers\CreateCategoriaNoticiaHandler;
use App\Application\CategoriasNoticia\Handlers\DeleteCategoriaNoticiaHandler;
use App\Application\CategoriasNoticia\Handlers\UpdateCategoriaNoticiaHandler;
use App\Application\CategoriasNoticia\Queries\GetCategoriaNoticiaByIdQuery;
use App\Application\CategoriasNoticia\Queries\GetCategoriasNoticiaQuery;
use App\Application\CategoriasNoticia\QueryHandlers\GetCategoriaNoticiaByIdQueryHandler;
use App\Application\CategoriasNoticia\QueryHandlers\GetCategoriasNoticiaQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoriasNoticia\StoreCategoriaNoticiaRequest;
use App\Http\Requests\CategoriasNoticia\UpdateCategoriaNoticiaRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoriaNoticiaController extends Controller
{
    public function __construct(
        private readonly GetCategoriasNoticiaQueryHandler $getCategoriasHandler,
        private readonly GetCategoriaNoticiaByIdQueryHandler $getCategoriaByIdHandler,
        private readonly CreateCategoriaNoticiaHandler $createHandler,
        private readonly UpdateCategoriaNoticiaHandler $updateHandler,
        private readonly DeleteCategoriaNoticiaHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize' => $request->get('pageSize', 50),
            'query' => $request->get('query', ''),
            'sortKey' => $request->input('sort.key', 'nombre'),
            'sortOrder' => $request->input('sort.order', 'asc'),
        ]);

        return response()->json(
            $this->getCategoriasHandler->handle(new GetCategoriasNoticiaQuery($pagination))
        );
    }

    public function store(StoreCategoriaNoticiaRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateCategoriaNoticiaCommand(
            nombre: $request->nombre,
            descripcion: $request->descripcion,
            color_hex: $request->color_hex,
            activa: $request->boolean('activa', true),
        ));

        return response()->json($dto, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->getCategoriaByIdHandler->handle(new GetCategoriaNoticiaByIdQuery($id)));
    }

    public function update(UpdateCategoriaNoticiaRequest $request, int $id): JsonResponse
    {
        return response()->json($this->updateHandler->handle(new UpdateCategoriaNoticiaCommand($id, $request->validated())));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteCategoriaNoticiaCommand($id));

        return response()->json(null, 204);
    }
}
