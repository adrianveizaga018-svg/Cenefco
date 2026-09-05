<?php

namespace App\Http\Controllers\Api;

use App\Application\EfectosEspeciales\Commands\CreateEfectoEspecialCommand;
use App\Application\EfectosEspeciales\Commands\DeleteEfectoEspecialCommand;
use App\Application\EfectosEspeciales\Commands\UpdateEfectoEspecialCommand;
use App\Application\EfectosEspeciales\Handlers\CreateEfectoEspecialHandler;
use App\Application\EfectosEspeciales\Handlers\DeleteEfectoEspecialHandler;
use App\Application\EfectosEspeciales\Handlers\UpdateEfectoEspecialHandler;
use App\Application\EfectosEspeciales\Queries\GetEfectoEspecialByIdQuery;
use App\Application\EfectosEspeciales\Queries\GetEfectosActivosQuery;
use App\Application\EfectosEspeciales\Queries\GetEfectosEspecialesQuery;
use App\Application\EfectosEspeciales\QueryHandlers\GetEfectoEspecialByIdQueryHandler;
use App\Application\EfectosEspeciales\QueryHandlers\GetEfectosActivosQueryHandler;
use App\Application\EfectosEspeciales\QueryHandlers\GetEfectosEspecialesQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\EfectosEspeciales\StoreEfectoEspecialRequest;
use App\Http\Requests\EfectosEspeciales\UpdateEfectoEspecialRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EfectoEspecialController extends Controller
{
    public function __construct(
        private readonly GetEfectosEspecialesQueryHandler  $getListHandler,
        private readonly GetEfectoEspecialByIdQueryHandler $getByIdHandler,
        private readonly GetEfectosActivosQueryHandler     $getActivosHandler,
        private readonly CreateEfectoEspecialHandler       $createHandler,
        private readonly UpdateEfectoEspecialHandler       $updateHandler,
        private readonly DeleteEfectoEspecialHandler       $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 20),
            'query'     => $request->get('query', ''),
            'sortKey'   => $request->input('sort.key', 'fecha_inicio'),
            'sortOrder' => $request->input('sort.order', 'asc'),
        ]);

        return response()->json(
            $this->getListHandler->handle(new GetEfectosEspecialesQuery($pagination))
        );
    }

    public function activos(): JsonResponse
    {
        return response()->json(['data' => $this->getActivosHandler->handle(new GetEfectosActivosQuery())]);
    }

    public function store(StoreEfectoEspecialRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateEfectoEspecialCommand(
            nombre:           $request->nombre,
            tipo_efecto:      $request->tipo_efecto,
            fecha_inicio:     $request->fecha_inicio,
            fecha_fin:        $request->fecha_fin,
            color_primario:   $request->color_primario   ?? '#ffffff',
            color_secundario: $request->color_secundario ?? null,
            intensidad:       $request->integer('intensidad', 50),
            activo:           $request->boolean('activo', true),
        ));

        return response()->json($dto, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->getByIdHandler->handle(new GetEfectoEspecialByIdQuery($id))
        );
    }

    public function update(UpdateEfectoEspecialRequest $request, int $id): JsonResponse
    {
        return response()->json(
            $this->updateHandler->handle(new UpdateEfectoEspecialCommand($id, $request->validated()))
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteEfectoEspecialCommand($id));

        return response()->json(null, 204);
    }
}
