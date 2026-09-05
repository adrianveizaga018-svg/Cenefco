<?php

namespace App\Http\Controllers\Api;

use App\Application\CifrasInstitucionales\Commands\CreateCifraInstitucionalCommand;
use App\Application\CifrasInstitucionales\Commands\DeleteCifraInstitucionalCommand;
use App\Application\CifrasInstitucionales\Commands\UpdateCifraInstitucionalCommand;
use App\Application\CifrasInstitucionales\Handlers\CreateCifraInstitucionalHandler;
use App\Application\CifrasInstitucionales\Handlers\DeleteCifraInstitucionalHandler;
use App\Application\CifrasInstitucionales\Handlers\UpdateCifraInstitucionalHandler;
use App\Application\CifrasInstitucionales\Queries\GetCifraInstitucionalByIdQuery;
use App\Application\CifrasInstitucionales\Queries\GetCifrasInstitucionalQuery;
use App\Application\CifrasInstitucionales\QueryHandlers\GetCifraInstitucionalByIdQueryHandler;
use App\Application\CifrasInstitucionales\QueryHandlers\GetCifrasInstitucionalQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\CifrasInstitucionales\StoreCifraInstitucionalRequest;
use App\Http\Requests\CifrasInstitucionales\UpdateCifraInstitucionalRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CifraInstitucionalController extends Controller
{
    public function __construct(
        private readonly GetCifrasInstitucionalQueryHandler $getCifrasHandler,
        private readonly GetCifraInstitucionalByIdQueryHandler $getByIdHandler,
        private readonly CreateCifraInstitucionalHandler $createHandler,
        private readonly UpdateCifraInstitucionalHandler $updateHandler,
        private readonly DeleteCifraInstitucionalHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 15),
            'query'     => $request->get('query', ''),
            'sortKey'   => $request->input('sort.key', 'orden'),
            'sortOrder' => $request->input('sort.order', 'asc'),
        ]);

        return response()->json($this->getCifrasHandler->handle(new GetCifrasInstitucionalQuery($pagination)));
    }

    public function store(StoreCifraInstitucionalRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateCifraInstitucionalCommand(
            valor:       $request->valor,
            etiqueta:    $request->etiqueta,
            descripcion: $request->descripcion,
            icono:       $request->icono,
            color:       $request->color,
            orden:       $request->integer('orden', 0),
            activo:      $request->boolean('activo', true),
        ));

        return response()->json($dto, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->getByIdHandler->handle(new GetCifraInstitucionalByIdQuery($id)));
    }

    public function update(UpdateCifraInstitucionalRequest $request, int $id): JsonResponse
    {
        return response()->json($this->updateHandler->handle(new UpdateCifraInstitucionalCommand(
            id:          $id,
            valor:       $request->valor,
            etiqueta:    $request->etiqueta,
            descripcion: $request->descripcion,
            icono:       $request->icono,
            color:       $request->color,
            orden:       $request->has('orden') ? $request->integer('orden') : null,
            activo:      $request->has('activo') ? $request->boolean('activo') : null,
        )));
    }

    public function indexPublico(): JsonResponse
    {
        return response()->json(
            $this->getCifrasHandler->handle(new GetCifrasInstitucionalQuery(publicoSoloActivas: true))
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteCifraInstitucionalCommand($id));

        return response()->json(null, 204);
    }
}
