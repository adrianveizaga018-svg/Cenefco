<?php

namespace App\Http\Controllers\Api;

use App\Application\PreguntasFrecuentes\Commands\CreatePreguntaFrecuenteCommand;
use App\Application\PreguntasFrecuentes\Commands\DeletePreguntaFrecuenteCommand;
use App\Application\PreguntasFrecuentes\Commands\UpdatePreguntaFrecuenteCommand;
use App\Application\PreguntasFrecuentes\Handlers\CreatePreguntaFrecuenteHandler;
use App\Application\PreguntasFrecuentes\Handlers\DeletePreguntaFrecuenteHandler;
use App\Application\PreguntasFrecuentes\Handlers\UpdatePreguntaFrecuenteHandler;
use App\Application\PreguntasFrecuentes\Queries\GetPreguntaFrecuenteByIdQuery;
use App\Application\PreguntasFrecuentes\Queries\GetPreguntasFrecuentesQuery;
use App\Application\PreguntasFrecuentes\QueryHandlers\GetPreguntaFrecuenteByIdQueryHandler;
use App\Application\PreguntasFrecuentes\QueryHandlers\GetPreguntasFrecuentesQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\PreguntasFrecuentes\StorePreguntaFrecuenteRequest;
use App\Http\Requests\PreguntasFrecuentes\UpdatePreguntaFrecuenteRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PreguntaFrecuenteController extends Controller
{
    public function __construct(
        private readonly GetPreguntasFrecuentesQueryHandler $getAllHandler,
        private readonly GetPreguntaFrecuenteByIdQueryHandler $getByIdHandler,
        private readonly CreatePreguntaFrecuenteHandler $createHandler,
        private readonly UpdatePreguntaFrecuenteHandler $updateHandler,
        private readonly DeletePreguntaFrecuenteHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize' => $request->get('pageSize', 20),
            'query' => $request->get('query', ''),
            'sortKey' => $request->input('sort.key', 'orden'),
            'sortOrder' => $request->input('sort.order', 'asc'),
        ]);

        return response()->json(
            $this->getAllHandler->handle(new GetPreguntasFrecuentesQuery(
                $pagination,
                $request->boolean('soloActivos', false),
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->getByIdHandler->handle(new GetPreguntaFrecuenteByIdQuery($id))
        );
    }

    public function store(StorePreguntaFrecuenteRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreatePreguntaFrecuenteCommand(
            pregunta: $request->pregunta,
            respuesta: $request->respuesta,
            categoria: $request->categoria,
            orden: (int) $request->get('orden', 0),
            activo: $request->boolean('activo', true),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdatePreguntaFrecuenteRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdatePreguntaFrecuenteCommand(
            id:        $id,
            pregunta:  $request->pregunta,
            respuesta: $request->respuesta,
            categoria: $request->categoria,
            orden:     $request->has('orden') ? $request->integer('orden') : null,
            activo:    $request->has('activo') ? $request->boolean('activo') : null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeletePreguntaFrecuenteCommand($id));

        return response()->json(null, 204);
    }
}
