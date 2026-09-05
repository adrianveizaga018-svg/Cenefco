<?php

namespace App\Http\Controllers\Api;

use App\Application\MediosPago\Commands\CreateMedioPagoCommand;
use App\Application\MediosPago\Commands\DeleteMedioPagoCommand;
use App\Application\MediosPago\Commands\UpdateMedioPagoCommand;
use App\Application\MediosPago\Handlers\CreateMedioPagoHandler;
use App\Application\MediosPago\Handlers\DeleteMedioPagoHandler;
use App\Application\MediosPago\Handlers\UpdateMedioPagoHandler;
use App\Application\MediosPago\Queries\GetMedioPagoByIdQuery;
use App\Application\MediosPago\Queries\GetMediosPagoQuery;
use App\Application\MediosPago\QueryHandlers\GetMedioPagoByIdQueryHandler;
use App\Application\MediosPago\QueryHandlers\GetMediosPagoQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\MediosPago\StoreMedioPagoRequest;
use App\Http\Requests\MediosPago\UpdateMedioPagoRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MedioPagoController extends Controller
{
    public function __construct(
        private readonly GetMediosPagoQueryHandler   $getMediosPagoHandler,
        private readonly GetMedioPagoByIdQueryHandler $getByIdHandler,
        private readonly CreateMedioPagoHandler       $createHandler,
        private readonly UpdateMedioPagoHandler       $updateHandler,
        private readonly DeleteMedioPagoHandler       $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->integer('pageIndex', 1),
            'pageSize'  => $request->integer('pageSize', 50),
        ]);

        return response()->json(
            $this->getMediosPagoHandler->handle(new GetMediosPagoQuery(
                pagination: $pagination,
                query:      $request->input('query'),
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->getByIdHandler->handle(new GetMedioPagoByIdQuery($id))
        );
    }

    public function indexPublico(): JsonResponse
    {
        return response()->json(
            $this->getMediosPagoHandler->handle(new GetMediosPagoQuery(
                pagination:  PaginationDTO::fromArray(['pageIndex' => 1, 'pageSize' => 200]),
                query:       null,
                soloActivos: true,
            ))
        );
    }

    public function store(StoreMedioPagoRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateMedioPagoCommand(
            nombre: $request->input('nombre'),
            orden:  $request->integer('orden', 0),
            activo: $request->boolean('activo', true),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateMedioPagoRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateMedioPagoCommand(
            id:     $id,
            nombre: $request->input('nombre'),
            orden:  $request->has('orden') ? $request->integer('orden') : null,
            activo: $request->has('activo') ? $request->boolean('activo') : null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteMedioPagoCommand($id));
        return response()->json(null, 204);
    }
}
