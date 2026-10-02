<?php

namespace App\Http\Controllers\FormulariosAcademicos;

use App\Application\FormulariosAcademicos\Commands\CreateFormularioAcademicoCommand;
use App\Application\FormulariosAcademicos\Commands\DeleteFormularioAcademicoCommand;
use App\Application\FormulariosAcademicos\Commands\UpdateFormularioAcademicoCommand;
use App\Application\FormulariosAcademicos\Handlers\CreateFormularioAcademicoHandler;
use App\Application\FormulariosAcademicos\Handlers\DeleteFormularioAcademicoHandler;
use App\Application\FormulariosAcademicos\Handlers\UpdateFormularioAcademicoHandler;
use App\Application\FormulariosAcademicos\Queries\GetFormularioAcademicoByIdQuery;
use App\Application\FormulariosAcademicos\Queries\GetFormulariosAcademicosQuery;
use App\Application\FormulariosAcademicos\QueryHandlers\GetFormularioAcademicoByIdQueryHandler;
use App\Application\FormulariosAcademicos\QueryHandlers\GetFormulariosAcademicosQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\FormulariosAcademicos\StoreFormularioAcademicoRequest;
use App\Http\Requests\FormulariosAcademicos\UpdateFormularioAcademicoRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FormularioAcademicoController extends Controller
{
    public function __construct(
        private readonly GetFormulariosAcademicosQueryHandler $listHandler,
        private readonly GetFormularioAcademicoByIdQueryHandler $showHandler,
        private readonly CreateFormularioAcademicoHandler $createHandler,
        private readonly UpdateFormularioAcademicoHandler $updateHandler,
        private readonly DeleteFormularioAcademicoHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 15),
        ]);

        return response()->json($this->listHandler->handle(new GetFormulariosAcademicosQuery(
            pagination:   $pagination,
            conInactivos: (bool) $request->get('conInactivos', false),
        )));
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->showHandler->handle(new GetFormularioAcademicoByIdQuery($id)));
    }

    public function store(StoreFormularioAcademicoRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateFormularioAcademicoCommand(
            idFormulario: $request->integer('id_formulario'),
            nombre:       $request->string('nombre')->toString(),
            descripcion:  $request->input('descripcion'),
            tipo:         $request->input('tipo'),
            idUsReg:      auth()->id(),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateFormularioAcademicoRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateFormularioAcademicoCommand(
            idFormulario: $id,
            nombre:       $request->input('nombre'),
            descripcion:  $request->input('descripcion'),
            tipo:         $request->input('tipo'),
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteFormularioAcademicoCommand($id));

        return response()->json(null, 204);
    }
}
