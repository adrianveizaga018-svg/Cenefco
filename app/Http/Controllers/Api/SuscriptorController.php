<?php

namespace App\Http\Controllers\Api;

use App\Application\Suscriptores\Commands\CreateSuscriptorCommand;
use App\Application\Suscriptores\Commands\DeleteSuscriptorCommand;
use App\Application\Suscriptores\Commands\UpdateSuscriptorCommand;
use App\Application\Suscriptores\Handlers\CreateSuscriptorHandler;
use App\Application\Suscriptores\Handlers\DeleteSuscriptorHandler;
use App\Application\Suscriptores\Handlers\UpdateSuscriptorHandler;
use App\Application\Suscriptores\Queries\GetSuscriptorByIdQuery;
use App\Application\Suscriptores\Queries\GetSuscriptoresQuery;
use App\Application\Suscriptores\QueryHandlers\GetSuscriptorByIdQueryHandler;
use App\Application\Suscriptores\QueryHandlers\GetSuscriptoresQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Suscriptores\StoreSuscriptorRequest;
use App\Http\Requests\Suscriptores\UpdateSuscriptorRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SuscriptorController extends Controller
{
    public function __construct(
        private readonly GetSuscriptoresQueryHandler $getSuscriptoresHandler,
        private readonly GetSuscriptorByIdQueryHandler $getByIdHandler,
        private readonly CreateSuscriptorHandler $createHandler,
        private readonly UpdateSuscriptorHandler $updateHandler,
        private readonly DeleteSuscriptorHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 20),
            'query'     => $request->get('query', ''),
            'sortKey'   => $request->input('sort.key', 'id'),
            'sortOrder' => $request->input('sort.order', 'desc'),
        ]);

        return response()->json($this->getSuscriptoresHandler->handle(new GetSuscriptoresQuery($pagination)));
    }

    public function store(StoreSuscriptorRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateSuscriptorCommand(
            email:      $request->email,
            nombre:     $request->nombre,
            origen:     $request->origen,
            activo:     true,
            confirmado: false,
        ));

        return response()->json($dto, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->getByIdHandler->handle(new GetSuscriptorByIdQuery($id)));
    }

    public function update(UpdateSuscriptorRequest $request, int $id): JsonResponse
    {
        return response()->json($this->updateHandler->handle(new UpdateSuscriptorCommand(
            id:         $id,
            nombre:     $request->nombre,
            confirmado: $request->has('confirmado') ? $request->boolean('confirmado') : null,
            activo:     $request->has('activo') ? $request->boolean('activo') : null,
        )));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteSuscriptorCommand($id));

        return response()->json(null, 204);
    }
}
