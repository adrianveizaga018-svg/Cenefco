<?php

namespace App\Http\Controllers\Api;

use App\Application\GradosAcademicos\Commands\CreateGradoAcademicoCommand;
use App\Application\GradosAcademicos\Commands\DeleteGradoAcademicoCommand;
use App\Application\GradosAcademicos\Commands\UpdateGradoAcademicoCommand;
use App\Application\GradosAcademicos\Handlers\CreateGradoAcademicoHandler;
use App\Application\GradosAcademicos\Handlers\DeleteGradoAcademicoHandler;
use App\Application\GradosAcademicos\Handlers\UpdateGradoAcademicoHandler;
use App\Application\GradosAcademicos\Queries\GetGradoAcademicoByIdQuery;
use App\Application\GradosAcademicos\Queries\GetGradosAcademicosQuery;
use App\Application\GradosAcademicos\QueryHandlers\GetGradoAcademicoByIdQueryHandler;
use App\Application\GradosAcademicos\QueryHandlers\GetGradosAcademicosQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\GradosAcademicos\StoreGradoAcademicoRequest;
use App\Http\Requests\GradosAcademicos\UpdateGradoAcademicoRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GradoAcademicoController extends Controller
{
    public function __construct(
        private readonly GetGradosAcademicosQueryHandler   $getGradosHandler,
        private readonly GetGradoAcademicoByIdQueryHandler $getByIdHandler,
        private readonly CreateGradoAcademicoHandler       $createHandler,
        private readonly UpdateGradoAcademicoHandler       $updateHandler,
        private readonly DeleteGradoAcademicoHandler       $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->integer('pageIndex', 1),
            'pageSize'  => $request->integer('pageSize', 50),
        ]);

        return response()->json(
            $this->getGradosHandler->handle(new GetGradosAcademicosQuery(
                pagination: $pagination,
                query:      $request->input('query'),
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->getByIdHandler->handle(new GetGradoAcademicoByIdQuery($id))
        );
    }

    public function indexPublico(): JsonResponse
    {
        return response()->json(
            $this->getGradosHandler->handle(new GetGradosAcademicosQuery(
                pagination:  PaginationDTO::fromArray(['pageIndex' => 1, 'pageSize' => 100]),
                query:       null,
                soloActivos: true,
            ))
        );
    }

    public function store(StoreGradoAcademicoRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateGradoAcademicoCommand(
            nombre:         $request->input('nombre'),
            abreviatura:    $request->input('abreviatura'),
            requiereTitulo: $request->boolean('requiere_titulo', false),
            orden:          $request->integer('orden', 0),
            activo:         $request->boolean('activo', true),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateGradoAcademicoRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateGradoAcademicoCommand(
            id:             $id,
            nombre:         $request->input('nombre'),
            abreviatura:    $request->input('abreviatura'),
            requiereTitulo: $request->has('requiere_titulo') ? $request->boolean('requiere_titulo') : null,
            orden:          $request->has('orden') ? $request->integer('orden') : null,
            activo:         $request->has('activo') ? $request->boolean('activo') : null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteGradoAcademicoCommand($id));
        return response()->json(null, 204);
    }
}
