<?php

namespace App\Http\Controllers\GruposAcademicos;

use App\Application\GruposAcademicos\Commands\CreateGrupoAcademicoCommand;
use App\Application\GruposAcademicos\Commands\DeleteGrupoAcademicoCommand;
use App\Application\GruposAcademicos\Commands\UpdateGrupoAcademicoCommand;
use App\Application\GruposAcademicos\Handlers\CreateGrupoAcademicoHandler;
use App\Application\GruposAcademicos\Handlers\DeleteGrupoAcademicoHandler;
use App\Application\GruposAcademicos\Handlers\UpdateGrupoAcademicoHandler;
use App\Application\GruposAcademicos\Queries\GetGrupoAcademicoByIdQuery;
use App\Application\GruposAcademicos\Queries\GetGruposAcademicosQuery;
use App\Application\GruposAcademicos\QueryHandlers\GetGrupoAcademicoByIdQueryHandler;
use App\Application\GruposAcademicos\QueryHandlers\GetGruposAcademicosQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\GruposAcademicos\StoreGrupoAcademicoRequest;
use App\Http\Requests\GruposAcademicos\UpdateGrupoAcademicoRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GrupoAcademicoController extends Controller
{
    public function __construct(
        private readonly GetGruposAcademicosQueryHandler $listHandler,
        private readonly GetGrupoAcademicoByIdQueryHandler $showHandler,
        private readonly CreateGrupoAcademicoHandler $createHandler,
        private readonly UpdateGrupoAcademicoHandler $updateHandler,
        private readonly DeleteGrupoAcademicoHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 15),
        ]);

        return response()->json($this->listHandler->handle(new GetGruposAcademicosQuery(
            pagination:   $pagination,
            query:        $request->input('query'),
            idTest:       $request->integer('id_test') ?: null,
            conInactivos: (bool) $request->get('conInactivos', false),
        )));
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->showHandler->handle(new GetGrupoAcademicoByIdQuery($id)));
    }

    public function store(StoreGrupoAcademicoRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateGrupoAcademicoCommand(
            idGrupo:     $request->integer('id_grupo'),
            idTest:      $request->integer('id_test'),
            nombre:      $request->string('nombre')->toString(),
            descripcion: $request->input('descripcion'),
            orden:       $request->integer('orden') ?: null,
            idUsReg:     auth()->id(),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateGrupoAcademicoRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateGrupoAcademicoCommand(
            idGrupo:     $id,
            idTest:      $request->integer('id_test') ?: null,
            nombre:      $request->input('nombre'),
            descripcion: $request->input('descripcion'),
            orden:       $request->integer('orden') ?: null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteGrupoAcademicoCommand($id));

        return response()->json(null, 204);
    }
}
