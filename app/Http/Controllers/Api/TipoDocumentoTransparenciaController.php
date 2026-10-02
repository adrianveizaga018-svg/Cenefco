<?php

namespace App\Http\Controllers\Api;

use App\Application\TiposDocumentoTransparencia\Commands\CreateTipoDocumentoTransparenciaCommand;
use App\Application\TiposDocumentoTransparencia\Commands\DeleteTipoDocumentoTransparenciaCommand;
use App\Application\TiposDocumentoTransparencia\Commands\UpdateTipoDocumentoTransparenciaCommand;
use App\Application\TiposDocumentoTransparencia\Handlers\CreateTipoDocumentoTransparenciaHandler;
use App\Application\TiposDocumentoTransparencia\Handlers\DeleteTipoDocumentoTransparenciaHandler;
use App\Application\TiposDocumentoTransparencia\Handlers\UpdateTipoDocumentoTransparenciaHandler;
use App\Application\TiposDocumentoTransparencia\Queries\GetTipoDocumentoTransparenciaByIdQuery;
use App\Application\TiposDocumentoTransparencia\Queries\GetTiposDocumentoTransparenciaQuery;
use App\Application\TiposDocumentoTransparencia\QueryHandlers\GetTipoDocumentoTransparenciaByIdQueryHandler;
use App\Application\TiposDocumentoTransparencia\QueryHandlers\GetTiposDocumentoTransparenciaQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\TiposDocumentoTransparencia\StoreTipoDocumentoTransparenciaRequest;
use App\Http\Requests\TiposDocumentoTransparencia\UpdateTipoDocumentoTransparenciaRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TipoDocumentoTransparenciaController extends Controller
{
    public function __construct(
        private readonly GetTiposDocumentoTransparenciaQueryHandler $getTiposHandler,
        private readonly GetTipoDocumentoTransparenciaByIdQueryHandler $getTipoByIdHandler,
        private readonly CreateTipoDocumentoTransparenciaHandler $createHandler,
        private readonly UpdateTipoDocumentoTransparenciaHandler $updateHandler,
        private readonly DeleteTipoDocumentoTransparenciaHandler $deleteHandler,
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
            $this->getTiposHandler->handle(new GetTiposDocumentoTransparenciaQuery($pagination))
        );
    }

    public function store(StoreTipoDocumentoTransparenciaRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateTipoDocumentoTransparenciaCommand(
            nombre: $request->nombre,
            activo: $request->boolean('activo', true),
        ));

        return response()->json($dto, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->getTipoByIdHandler->handle(new GetTipoDocumentoTransparenciaByIdQuery($id)));
    }

    public function update(UpdateTipoDocumentoTransparenciaRequest $request, int $id): JsonResponse
    {
        return response()->json($this->updateHandler->handle(new UpdateTipoDocumentoTransparenciaCommand($id, $request->validated())));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteTipoDocumentoTransparenciaCommand($id));

        return response()->json(null, 204);
    }
}
