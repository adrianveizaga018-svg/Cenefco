<?php

namespace App\Http\Controllers\ArchivosAcademicos;

use App\Application\ArchivosAcademicos\Commands\CreateArchivoAcademicoCommand;
use App\Application\ArchivosAcademicos\Commands\DeleteArchivoAcademicoCommand;
use App\Application\ArchivosAcademicos\Commands\UpdateArchivoAcademicoCommand;
use App\Application\ArchivosAcademicos\Handlers\CreateArchivoAcademicoHandler;
use App\Application\ArchivosAcademicos\Handlers\DeleteArchivoAcademicoHandler;
use App\Application\ArchivosAcademicos\Handlers\UpdateArchivoAcademicoHandler;
use App\Application\ArchivosAcademicos\Queries\GetArchivoAcademicoByIdQuery;
use App\Application\ArchivosAcademicos\Queries\GetArchivosAcademicosQuery;
use App\Application\ArchivosAcademicos\QueryHandlers\GetArchivoAcademicoByIdQueryHandler;
use App\Application\ArchivosAcademicos\QueryHandlers\GetArchivosAcademicosQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\ArchivosAcademicos\StoreArchivoAcademicoRequest;
use App\Http\Requests\ArchivosAcademicos\UpdateArchivoAcademicoRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArchivoAcademicoController extends Controller
{
    public function __construct(
        private readonly GetArchivosAcademicosQueryHandler $listHandler,
        private readonly GetArchivoAcademicoByIdQueryHandler $showHandler,
        private readonly CreateArchivoAcademicoHandler $createHandler,
        private readonly UpdateArchivoAcademicoHandler $updateHandler,
        private readonly DeleteArchivoAcademicoHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 15),
        ]);

        return response()->json($this->listHandler->handle(new GetArchivosAcademicosQuery(
            pagination:   $pagination,
            conInactivos: (bool) $request->get('conInactivos', false),
        )));
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->showHandler->handle(new GetArchivoAcademicoByIdQuery($id)));
    }

    public function store(StoreArchivoAcademicoRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateArchivoAcademicoCommand(
            idArch:      $request->integer('id_arch'),
            nombre:      $request->string('nombre')->toString(),
            descripcion: $request->input('descripcion'),
            url:         $request->string('url')->toString(),
            idTipo:      $request->integer('id_tipo') ?: null,
            idUsReg:     auth()->id(),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateArchivoAcademicoRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateArchivoAcademicoCommand(
            idArch:      $id,
            nombre:      $request->input('nombre'),
            descripcion: $request->input('descripcion'),
            url:         $request->input('url'),
            idTipo:      $request->integer('id_tipo') ?: null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteArchivoAcademicoCommand($id));

        return response()->json(null, 204);
    }
}
