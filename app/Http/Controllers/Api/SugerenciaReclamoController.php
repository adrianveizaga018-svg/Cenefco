<?php

namespace App\Http\Controllers\Api;

use App\Application\SugerenciasReclamos\Commands\CreateSugerenciaReclamoCommand;
use App\Application\SugerenciasReclamos\Commands\DeleteSugerenciaReclamoCommand;
use App\Application\SugerenciasReclamos\Commands\RespondSugerenciaReclamoCommand;
use App\Application\SugerenciasReclamos\Handlers\CreateSugerenciaReclamoHandler;
use App\Application\SugerenciasReclamos\Handlers\DeleteSugerenciaReclamoHandler;
use App\Application\SugerenciasReclamos\Handlers\RespondSugerenciaReclamoHandler;
use App\Application\SugerenciasReclamos\Queries\GetSugerenciaReclamoByIdQuery;
use App\Application\SugerenciasReclamos\Queries\GetSugerenciasReclamosQuery;
use App\Application\SugerenciasReclamos\QueryHandlers\GetSugerenciaReclamoByIdQueryHandler;
use App\Application\SugerenciasReclamos\QueryHandlers\GetSugerenciasReclamosQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\SugerenciasReclamos\RespondSugerenciaReclamoRequest;
use App\Http\Requests\SugerenciasReclamos\StoreSugerenciaReclamoRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SugerenciaReclamoController extends Controller
{
    public function __construct(
        private readonly GetSugerenciasReclamosQueryHandler $getSugerenciasHandler,
        private readonly GetSugerenciaReclamoByIdQueryHandler $getSugerenciaByIdHandler,
        private readonly CreateSugerenciaReclamoHandler $createHandler,
        private readonly RespondSugerenciaReclamoHandler $respondHandler,
        private readonly DeleteSugerenciaReclamoHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize' => $request->get('pageSize', 15),
            'query' => $request->get('query', ''),
            'sortKey' => $request->input('sort.key', 'created_at'),
            'sortOrder' => $request->input('sort.order', 'desc'),
        ]);

        return response()->json(
            $this->getSugerenciasHandler->handle(new GetSugerenciasReclamosQuery($pagination))
        );
    }

    public function store(StoreSugerenciaReclamoRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateSugerenciaReclamoCommand(
            asunto: $request->asunto,
            mensaje: $request->mensaje,
            email_respuesta: $request->email_respuesta,
            secretaria_destino_id: $request->secretaria_destino_id ? (int) $request->secretaria_destino_id : null,
            usuario_id: auth()->id() ?? null,
        ));

        return response()->json($dto, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->getSugerenciaByIdHandler->handle(new GetSugerenciaReclamoByIdQuery($id)));
    }

    public function respond(RespondSugerenciaReclamoRequest $request, int $id): JsonResponse
    {
        $dto = $this->respondHandler->handle(new RespondSugerenciaReclamoCommand(
            id: $id,
            respuesta: $request->respuesta,
            respondido_por: auth()->id(),
            estado: $request->get('estado', 'resuelto'),
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteSugerenciaReclamoCommand($id));

        return response()->json(null, 204);
    }
}
