<?php

namespace App\Http\Controllers\Api;

use App\Application\Carreras\Commands\CreateCarreraCommand;
use App\Application\Carreras\Commands\DeleteCarreraCommand;
use App\Application\Carreras\Commands\UpdateCarreraCommand;
use App\Application\Carreras\Handlers\CreateCarreraHandler;
use App\Application\Carreras\Handlers\DeleteCarreraHandler;
use App\Application\Carreras\Handlers\UpdateCarreraHandler;
use App\Application\Carreras\Queries\GetCarreraByIdQuery;
use App\Application\Carreras\Queries\GetCarrerasQuery;
use App\Application\Carreras\QueryHandlers\GetCarreraByIdQueryHandler;
use App\Application\Carreras\QueryHandlers\GetCarrerasQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Carreras\StoreCarreraRequest;
use App\Http\Requests\Carreras\UpdateCarreraRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CarreraController extends Controller
{
    public function __construct(
        private readonly GetCarrerasQueryHandler $getCarrerasHandler,
        private readonly GetCarreraByIdQueryHandler $getCarreraByIdHandler,
        private readonly CreateCarreraHandler $createHandler,
        private readonly UpdateCarreraHandler $updateHandler,
        private readonly DeleteCarreraHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 50),
            'query'     => $request->get('query', ''),
            'sortKey'   => 'nombre_carrera',
            'sortOrder' => 'asc',
        ]);

        return response()->json(
            $this->getCarrerasHandler->handle(new GetCarrerasQuery(
                pagination:   $pagination,
                conInactivos: $request->boolean('conInactivos', false),
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->getCarreraByIdHandler->handle(new GetCarreraByIdQuery($id))
        );
    }

    public function store(StoreCarreraRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateCarreraCommand(
            id_carrera:    $request->integer('id_carrera'),
            id_us_reg:     $request->integer('id_us_reg', 0),
            num_carrera:   $request->filled('num_carrera') ? $request->integer('num_carrera') : 0,
            nombre_carrera: $request->nombre_carrera,
            estado:        $request->integer('estado', 1),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateCarreraRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateCarreraCommand(
            id:             $id,
            nombre_carrera: $request->nombre_carrera,
            estado:         $request->filled('estado') ? $request->integer('estado') : null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteCarreraCommand($id));

        return response()->json(null, 204);
    }
}
