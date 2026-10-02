<?php

namespace App\Http\Controllers\Api;

use App\Application\Profesiones\Commands\CreateProfesionCommand;
use App\Application\Profesiones\Commands\DeleteProfesionCommand;
use App\Application\Profesiones\Commands\UpdateProfesionCommand;
use App\Application\Profesiones\Handlers\CreateProfesionHandler;
use App\Application\Profesiones\Handlers\DeleteProfesionHandler;
use App\Application\Profesiones\Handlers\UpdateProfesionHandler;
use App\Application\Profesiones\Queries\GetProfesionByIdQuery;
use App\Application\Profesiones\Queries\GetProfesionesQuery;
use App\Application\Profesiones\QueryHandlers\GetProfesionByIdQueryHandler;
use App\Application\Profesiones\QueryHandlers\GetProfesionesQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profesiones\StoreProfesionRequest;
use App\Http\Requests\Profesiones\UpdateProfesionRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfesionController extends Controller
{
    public function __construct(
        private readonly GetProfesionesQueryHandler   $getProfesionesHandler,
        private readonly GetProfesionByIdQueryHandler $getByIdHandler,
        private readonly CreateProfesionHandler       $createHandler,
        private readonly UpdateProfesionHandler       $updateHandler,
        private readonly DeleteProfesionHandler       $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->integer('pageIndex', 1),
            'pageSize'  => $request->integer('pageSize', 50),
        ]);

        return response()->json(
            $this->getProfesionesHandler->handle(new GetProfesionesQuery(
                pagination: $pagination,
                query:      $request->input('query'),
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->getByIdHandler->handle(new GetProfesionByIdQuery($id))
        );
    }

    public function indexPublico(): JsonResponse
    {
        return response()->json(
            $this->getProfesionesHandler->handle(new GetProfesionesQuery(
                pagination:  PaginationDTO::fromArray(['pageIndex' => 1, 'pageSize' => 200]),
                query:       null,
                soloActivos: true,
            ))
        );
    }

    public function store(StoreProfesionRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateProfesionCommand(
            nombre: $request->input('nombre'),
            orden:  $request->integer('orden', 0),
            activo: $request->boolean('activo', true),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateProfesionRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateProfesionCommand(
            id:     $id,
            nombre: $request->input('nombre'),
            orden:  $request->has('orden') ? $request->integer('orden') : null,
            activo: $request->has('activo') ? $request->boolean('activo') : null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteProfesionCommand($id));
        return response()->json(null, 204);
    }
}
