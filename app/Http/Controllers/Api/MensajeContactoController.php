<?php

namespace App\Http\Controllers\Api;

use App\Application\MensajesContacto\Commands\CreateMensajeContactoCommand;
use App\Application\MensajesContacto\Commands\DeleteMensajeContactoCommand;
use App\Application\MensajesContacto\Commands\RespondMensajeContactoCommand;
use App\Application\MensajesContacto\Handlers\CreateMensajeContactoHandler;
use App\Application\MensajesContacto\Handlers\DeleteMensajeContactoHandler;
use App\Application\MensajesContacto\Handlers\RespondMensajeContactoHandler;
use App\Application\MensajesContacto\Queries\GetMensajeContactoByIdQuery;
use App\Application\MensajesContacto\Queries\GetMensajesContactoQuery;
use App\Application\MensajesContacto\QueryHandlers\GetMensajeContactoByIdQueryHandler;
use App\Application\MensajesContacto\QueryHandlers\GetMensajesContactoQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\MensajesContacto\RespondMensajeContactoRequest;
use App\Http\Requests\MensajesContacto\StoreMensajeContactoRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MensajeContactoController extends Controller
{
    public function __construct(
        private readonly GetMensajesContactoQueryHandler $getMensajesHandler,
        private readonly GetMensajeContactoByIdQueryHandler $getMensajeByIdHandler,
        private readonly CreateMensajeContactoHandler $createHandler,
        private readonly RespondMensajeContactoHandler $respondHandler,
        private readonly DeleteMensajeContactoHandler $deleteHandler,
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
            $this->getMensajesHandler->handle(new GetMensajesContactoQuery($pagination))
        );
    }

    public function store(StoreMensajeContactoRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateMensajeContactoCommand(
            nombre_remitente: $request->nombre_remitente,
            email_remitente: $request->email_remitente,
            asunto: $request->asunto,
            mensaje: $request->mensaje,
            telefono_remitente: $request->telefono_remitente,
            secretaria_destino_id: $request->secretaria_destino_id ? (int) $request->secretaria_destino_id : null,
            ip_origen: $request->ip(),
        ));

        return response()->json($dto, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->getMensajeByIdHandler->handle(new GetMensajeContactoByIdQuery($id))
        );
    }

    public function respond(RespondMensajeContactoRequest $request, int $id): JsonResponse
    {
        $dto = $this->respondHandler->handle(new RespondMensajeContactoCommand(
            id: $id,
            respuesta: $request->respuesta,
            respondido_por: auth()->id(),
            estado: $request->get('estado', 'respondido'),
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteMensajeContactoCommand($id));

        return response()->json(null, 204);
    }
}
