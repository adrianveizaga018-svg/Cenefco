<?php
namespace App\Http\Controllers\Api;
use App\Application\ModulosAcademicos\Commands\CreateModuloAcademicoCommand;
use App\Application\ModulosAcademicos\Commands\DeleteModuloAcademicoCommand;
use App\Application\ModulosAcademicos\Commands\UpdateModuloAcademicoCommand;
use App\Application\ModulosAcademicos\Handlers\CreateModuloAcademicoHandler;
use App\Application\ModulosAcademicos\Handlers\DeleteModuloAcademicoHandler;
use App\Application\ModulosAcademicos\Handlers\UpdateModuloAcademicoHandler;
use App\Application\ModulosAcademicos\Queries\GetModuloAcademicoByIdQuery;
use App\Application\ModulosAcademicos\Queries\GetModulosAcademicosQuery;
use App\Application\ModulosAcademicos\QueryHandlers\GetModuloAcademicoByIdQueryHandler;
use App\Application\ModulosAcademicos\QueryHandlers\GetModulosAcademicosQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\ModulosAcademicos\StoreModuloAcademicoRequest;
use App\Http\Requests\ModulosAcademicos\UpdateModuloAcademicoRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class ModuloAcademicoController extends Controller {
    public function __construct(
        private readonly GetModulosAcademicosQueryHandler $listHandler,
        private readonly GetModuloAcademicoByIdQueryHandler $showHandler,
        private readonly CreateModuloAcademicoHandler $createHandler,
        private readonly UpdateModuloAcademicoHandler $updateHandler,
        private readonly DeleteModuloAcademicoHandler $deleteHandler,
    ) {}
    public function index(Request $request): JsonResponse {
        $pagination = PaginationDTO::fromArray(['pageIndex' => $request->get('pageIndex', 1), 'pageSize' => $request->get('pageSize', 15)]);
        return response()->json($this->listHandler->handle(new GetModulosAcademicosQuery(pagination: $pagination, query: $request->input('query'), posicion: $request->integer('posicion') ?: null, conInactivos: $request->boolean('con_inactivos'))));
    }
    public function show(int $id): JsonResponse { return response()->json($this->showHandler->handle(new GetModuloAcademicoByIdQuery($id))); }
    public function store(StoreModuloAcademicoRequest $request): JsonResponse {
        $dto = $this->createHandler->handle(new CreateModuloAcademicoCommand(idMod: $request->integer('id_mod'), nombre: $request->input('nombre'), descripcion: $request->input('descripcion'), posicion: $request->integer('posicion') ?: null, idUsReg: auth()->id()));
        return response()->json($dto, 201);
    }
    public function update(UpdateModuloAcademicoRequest $request, int $id): JsonResponse {
        $this->updateHandler->handle(new UpdateModuloAcademicoCommand(idMod: $id, nombre: $request->input('nombre'), descripcion: $request->input('descripcion'), posicion: $request->integer('posicion') ?: null));
        return response()->json($this->showHandler->handle(new GetModuloAcademicoByIdQuery($id)));
    }
    public function destroy(int $id): JsonResponse {
        $this->deleteHandler->handle(new DeleteModuloAcademicoCommand($id));
        return response()->json(null, 204);
    }
}
