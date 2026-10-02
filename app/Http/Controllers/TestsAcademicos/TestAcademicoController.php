<?php

namespace App\Http\Controllers\TestsAcademicos;

use App\Application\TestsAcademicos\Commands\CreateTestAcademicoCommand;
use App\Application\TestsAcademicos\Commands\DeleteTestAcademicoCommand;
use App\Application\TestsAcademicos\Commands\UpdateTestAcademicoCommand;
use App\Application\TestsAcademicos\Handlers\CreateTestAcademicoHandler;
use App\Application\TestsAcademicos\Handlers\DeleteTestAcademicoHandler;
use App\Application\TestsAcademicos\Handlers\UpdateTestAcademicoHandler;
use App\Application\TestsAcademicos\Queries\GetTestAcademicoByIdQuery;
use App\Application\TestsAcademicos\Queries\GetTestsAcademicosQuery;
use App\Application\TestsAcademicos\QueryHandlers\GetTestAcademicoByIdQueryHandler;
use App\Application\TestsAcademicos\QueryHandlers\GetTestsAcademicosQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\TestsAcademicos\StoreTestAcademicoRequest;
use App\Http\Requests\TestsAcademicos\UpdateTestAcademicoRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TestAcademicoController extends Controller
{
    public function __construct(
        private readonly GetTestsAcademicosQueryHandler $listHandler,
        private readonly GetTestAcademicoByIdQueryHandler $showHandler,
        private readonly CreateTestAcademicoHandler $createHandler,
        private readonly UpdateTestAcademicoHandler $updateHandler,
        private readonly DeleteTestAcademicoHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 15),
        ]);

        return response()->json($this->listHandler->handle(new GetTestsAcademicosQuery(
            pagination:    $pagination,
            query:         $request->input('query'),
            habilitarTest: $request->has('habilitar_test') ? (bool) $request->get('habilitar_test') : null,
            conInactivos:  (bool) $request->get('conInactivos', false),
        )));
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->showHandler->handle(new GetTestAcademicoByIdQuery($id)));
    }

    public function store(StoreTestAcademicoRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateTestAcademicoCommand(
            idTest:       $request->integer('id_test'),
            nombre:       $request->string('nombre')->toString(),
            descripcion:  $request->input('descripcion'),
            habilitarTest: $request->boolean('habilitar_test', false),
            idUsReg:      auth()->id(),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateTestAcademicoRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateTestAcademicoCommand(
            idTest:        $id,
            nombre:        $request->input('nombre'),
            descripcion:   $request->input('descripcion'),
            habilitarTest: $request->has('habilitar_test') ? $request->boolean('habilitar_test') : null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteTestAcademicoCommand($id));

        return response()->json(null, 204);
    }
}
