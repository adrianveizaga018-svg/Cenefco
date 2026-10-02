<?php
namespace App\Http\Controllers\Api;
use App\Application\MenusAcademicos\Commands\CreateMenuAcademicoCommand;
use App\Application\MenusAcademicos\Commands\DeleteMenuAcademicoCommand;
use App\Application\MenusAcademicos\Commands\UpdateMenuAcademicoCommand;
use App\Application\MenusAcademicos\Handlers\CreateMenuAcademicoHandler;
use App\Application\MenusAcademicos\Handlers\DeleteMenuAcademicoHandler;
use App\Application\MenusAcademicos\Handlers\UpdateMenuAcademicoHandler;
use App\Application\MenusAcademicos\Queries\GetMenuAcademicoByIdQuery;
use App\Application\MenusAcademicos\Queries\GetMenusAcademicosQuery;
use App\Application\MenusAcademicos\QueryHandlers\GetMenuAcademicoByIdQueryHandler;
use App\Application\MenusAcademicos\QueryHandlers\GetMenusAcademicosQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\MenusAcademicos\StoreMenuAcademicoRequest;
use App\Http\Requests\MenusAcademicos\UpdateMenuAcademicoRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class MenuAcademicoController extends Controller {
    public function __construct(
        private readonly GetMenusAcademicosQueryHandler $listHandler,
        private readonly GetMenuAcademicoByIdQueryHandler $showHandler,
        private readonly CreateMenuAcademicoHandler $createHandler,
        private readonly UpdateMenuAcademicoHandler $updateHandler,
        private readonly DeleteMenuAcademicoHandler $deleteHandler,
    ) {}
    public function index(Request $request): JsonResponse {
        $pagination = PaginationDTO::fromArray(['pageIndex' => $request->get('pageIndex', 1), 'pageSize' => $request->get('pageSize', 15)]);
        return response()->json($this->listHandler->handle(new GetMenusAcademicosQuery(pagination: $pagination, query: $request->input('query'), idMod: $request->integer('id_mod') ?: null, conInactivos: $request->boolean('con_inactivos'))));
    }
    public function show(int $id): JsonResponse { return response()->json($this->showHandler->handle(new GetMenuAcademicoByIdQuery($id))); }
    public function store(StoreMenuAcademicoRequest $request): JsonResponse {
        $dto = $this->createHandler->handle(new CreateMenuAcademicoCommand(idMen: $request->integer('id_men'), idMod: $request->integer('id_mod') ?: null, nombre: $request->input('nombre'), descripcion: $request->input('descripcion'), idUsReg: auth()->id()));
        return response()->json($dto, 201);
    }
    public function update(UpdateMenuAcademicoRequest $request, int $id): JsonResponse {
        $this->updateHandler->handle(new UpdateMenuAcademicoCommand(idMen: $id, idMod: $request->integer('id_mod') ?: null, nombre: $request->input('nombre'), descripcion: $request->input('descripcion')));
        return response()->json($this->showHandler->handle(new GetMenuAcademicoByIdQuery($id)));
    }
    public function destroy(int $id): JsonResponse {
        $this->deleteHandler->handle(new DeleteMenuAcademicoCommand($id));
        return response()->json(null, 204);
    }
}
