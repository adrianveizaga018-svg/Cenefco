<?php

namespace App\Http\Controllers\FormulariosIns;

use App\Application\FormulariosIns\Commands\CreateFormularioInsCommand;
use App\Application\FormulariosIns\Commands\DeleteFormularioInsCommand;
use App\Application\FormulariosIns\Commands\UpdateFormularioInsCommand;
use App\Application\FormulariosIns\Handlers\CreateFormularioInsHandler;
use App\Application\FormulariosIns\Handlers\DeleteFormularioInsHandler;
use App\Application\FormulariosIns\Handlers\UpdateFormularioInsHandler;
use App\Application\FormulariosIns\Queries\GetFormularioInsByIdQuery;
use App\Application\FormulariosIns\Queries\GetFormulariosInsQuery;
use App\Application\FormulariosIns\QueryHandlers\GetFormularioInsByIdQueryHandler;
use App\Application\FormulariosIns\QueryHandlers\GetFormulariosInsQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\FormulariosIns\StoreFormularioInsRequest;
use App\Http\Requests\FormulariosIns\UpdateFormularioInsRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FormularioInsController extends Controller
{
    public function __construct(
        private readonly GetFormulariosInsQueryHandler $listHandler,
        private readonly GetFormularioInsByIdQueryHandler $showHandler,
        private readonly CreateFormularioInsHandler $createHandler,
        private readonly UpdateFormularioInsHandler $updateHandler,
        private readonly DeleteFormularioInsHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 15),
        ]);

        return response()->json($this->listHandler->handle(new GetFormulariosInsQuery(
            pagination:   $pagination,
            query:        $request->input('query'),
            idImp:        $request->integer('id_imp') ?: null,
            conInactivos: (bool) $request->get('conInactivos', false),
        )));
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->showHandler->handle(new GetFormularioInsByIdQuery($id)));
    }

    public function store(StoreFormularioInsRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateFormularioInsCommand(
            idFormins:   $request->integer('id_formins'),
            idImp:       $request->integer('id_imp'),
            nombre:      $request->string('nombre')->toString(),
            descripcion: $request->input('descripcion'),
            idUsReg:     auth()->id(),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateFormularioInsRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateFormularioInsCommand(
            idFormins:   $id,
            idImp:       $request->integer('id_imp') ?: null,
            nombre:      $request->input('nombre'),
            descripcion: $request->input('descripcion'),
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteFormularioInsCommand($id));

        return response()->json(null, 204);
    }
}
