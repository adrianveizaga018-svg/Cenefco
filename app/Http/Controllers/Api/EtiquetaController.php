<?php

namespace App\Http\Controllers\Api;

use App\Application\Etiquetas\Commands\CreateEtiquetaCommand;
use App\Application\Etiquetas\Commands\DeleteEtiquetaCommand;
use App\Application\Etiquetas\Commands\UpdateEtiquetaCommand;
use App\Application\Etiquetas\Handlers\CreateEtiquetaHandler;
use App\Application\Etiquetas\Handlers\DeleteEtiquetaHandler;
use App\Application\Etiquetas\Handlers\UpdateEtiquetaHandler;
use App\Application\Etiquetas\Queries\GetEtiquetaByIdQuery;
use App\Application\Etiquetas\Queries\GetEtiquetaBySlugQuery;
use App\Application\Etiquetas\Queries\GetEtiquetasQuery;
use App\Application\Etiquetas\QueryHandlers\GetEtiquetaByIdQueryHandler;
use App\Application\Etiquetas\QueryHandlers\GetEtiquetaBySlugQueryHandler;
use App\Application\Etiquetas\QueryHandlers\GetEtiquetasQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Etiquetas\StoreEtiquetaRequest;
use App\Http\Requests\Etiquetas\UpdateEtiquetaRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EtiquetaController extends Controller
{
    public function __construct(
        private readonly GetEtiquetasQueryHandler      $getEtiquetasHandler,
        private readonly GetEtiquetaByIdQueryHandler   $getEtiquetaByIdHandler,
        private readonly GetEtiquetaBySlugQueryHandler $getEtiquetaBySlugHandler,
        private readonly CreateEtiquetaHandler         $createHandler,
        private readonly UpdateEtiquetaHandler         $updateHandler,
        private readonly DeleteEtiquetaHandler         $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize' => $request->get('pageSize', 100),
            'query' => $request->get('query', ''),
            'sortKey' => $request->input('sort.key', 'nombre'),
            'sortOrder' => $request->input('sort.order', 'asc'),
        ]);

        return response()->json(
            $this->getEtiquetasHandler->handle(new GetEtiquetasQuery($pagination))
        );
    }

    public function store(StoreEtiquetaRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateEtiquetaCommand(
            nombre: $request->nombre,
        ));

        return response()->json($dto, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->getEtiquetaByIdHandler->handle(new GetEtiquetaByIdQuery($id)));
    }

    public function showBySlug(string $slug): JsonResponse
    {
        return response()->json($this->getEtiquetaBySlugHandler->handle(new GetEtiquetaBySlugQuery($slug)));
    }

    public function update(UpdateEtiquetaRequest $request, int $id): JsonResponse
    {
        return response()->json($this->updateHandler->handle(new UpdateEtiquetaCommand(
            id:     $id,
            nombre: $request->nombre,
            color:  $request->color,
        )));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteEtiquetaCommand($id));

        return response()->json(null, 204);
    }
}
