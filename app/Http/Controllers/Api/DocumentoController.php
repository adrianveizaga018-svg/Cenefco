<?php

namespace App\Http\Controllers\Api;

use App\Application\DocumentosTransparencia\Commands\CreateDocumentoCommand;
use App\Application\DocumentosTransparencia\Commands\DeleteDocumentoCommand;
use App\Application\DocumentosTransparencia\Commands\UpdateDocumentoCommand;
use App\Application\DocumentosTransparencia\Handlers\CreateDocumentoHandler;
use App\Application\DocumentosTransparencia\Handlers\DeleteDocumentoHandler;
use App\Application\DocumentosTransparencia\Handlers\UpdateDocumentoHandler;
use App\Application\DocumentosTransparencia\Queries\GetDocumentoByIdQuery;
use App\Application\DocumentosTransparencia\Queries\GetDocumentosQuery;
use App\Application\DocumentosTransparencia\QueryHandlers\GetDocumentoByIdQueryHandler;
use App\Application\DocumentosTransparencia\QueryHandlers\GetDocumentosQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentosTransparencia\StoreDocumentoRequest;
use App\Http\Requests\DocumentosTransparencia\UpdateDocumentoRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentoController extends Controller
{
    public function __construct(
        private readonly GetDocumentosQueryHandler $getDocumentosHandler,
        private readonly GetDocumentoByIdQueryHandler $getDocumentoByIdHandler,
        private readonly CreateDocumentoHandler $createHandler,
        private readonly UpdateDocumentoHandler $updateHandler,
        private readonly DeleteDocumentoHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize' => $request->get('pageSize', 20),
            'query' => $request->get('query', ''),
            'sortKey' => $request->input('sort.key', 'fecha_publicacion'),
            'sortOrder' => $request->input('sort.order', 'desc'),
        ]);

        return response()->json(
            $this->getDocumentosHandler->handle(
                new GetDocumentosQuery($pagination, $request->boolean('soloActivos', false))
            )
        );
    }

    public function store(StoreDocumentoRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateDocumentoCommand(
            tipo_documento_id: $request->tipo_documento_id,
            secretaria_id: $request->secretaria_id,
            publicado_por: auth()->id(),
            titulo: $request->titulo,
            descripcion: $request->descripcion,
            archivo_url: $request->archivo_url,
            gestion: $request->integer('gestion', date('Y')),
            fecha_publicacion: $request->get('fecha_publicacion', date('Y-m-d')),
            activo: $request->boolean('activo', true),
        ));

        return response()->json($dto, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->getDocumentoByIdHandler->handle(new GetDocumentoByIdQuery($id)));
    }

    public function update(UpdateDocumentoRequest $request, int $id): JsonResponse
    {
        return response()->json($this->updateHandler->handle(new UpdateDocumentoCommand($id, $request->validated())));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteDocumentoCommand($id));

        return response()->json(null, 204);
    }
}
