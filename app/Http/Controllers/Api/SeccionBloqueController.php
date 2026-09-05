<?php
namespace App\Http\Controllers\Api;
use App\Application\SeccionesBloque\Commands\CreateSeccionBloqueCommand;
use App\Application\SeccionesBloque\Commands\DeleteSeccionBloqueCommand;
use App\Application\SeccionesBloque\Commands\UpdateSeccionBloqueCommand;
use App\Application\SeccionesBloque\Handlers\CreateSeccionBloqueHandler;
use App\Application\SeccionesBloque\Handlers\DeleteSeccionBloqueHandler;
use App\Application\SeccionesBloque\Handlers\UpdateSeccionBloqueHandler;
use App\Application\SeccionesBloque\Queries\GetSeccionBloqueByIdQuery;
use App\Application\SeccionesBloque\Queries\GetSeccionesBloqueQuery;
use App\Application\SeccionesBloque\QueryHandlers\GetSeccionBloqueByIdQueryHandler;
use App\Application\SeccionesBloque\QueryHandlers\GetSeccionesBloqueQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\SeccionesBloque\StoreSeccionBloqueRequest;
use App\Http\Requests\SeccionesBloque\UpdateSeccionBloqueRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class SeccionBloqueController extends Controller {
    public function __construct(
        private readonly GetSeccionesBloqueQueryHandler $listHandler,
        private readonly GetSeccionBloqueByIdQueryHandler $showHandler,
        private readonly CreateSeccionBloqueHandler $createHandler,
        private readonly UpdateSeccionBloqueHandler $updateHandler,
        private readonly DeleteSeccionBloqueHandler $deleteHandler,
    ) {}
    public function index(Request $request): JsonResponse {
        $pagination = PaginationDTO::fromArray(['pageIndex' => $request->get('pageIndex', 1), 'pageSize' => $request->get('pageSize', 15)]);
        return response()->json($this->listHandler->handle(new GetSeccionesBloqueQuery(pagination: $pagination, idBloqueajustable: $request->integer('id_bloqueajustable') ?: null, conInactivos: $request->boolean('con_inactivos'))));
    }
    public function show(int $id): JsonResponse {
        return response()->json($this->showHandler->handle(new GetSeccionBloqueByIdQuery($id)));
    }
    public function store(StoreSeccionBloqueRequest $request): JsonResponse {
        $dto = $this->createHandler->handle(new CreateSeccionBloqueCommand(idSeccionbloque: $request->integer('id_seccionbloque'), idBloqueajustable: $request->integer('id_bloqueajustable') ?: null, titulo: $request->input('titulo'), contenido: $request->input('contenido'), idUsReg: auth()->id()));
        return response()->json($dto, 201);
    }
    public function update(UpdateSeccionBloqueRequest $request, int $id): JsonResponse {
        $this->updateHandler->handle(new UpdateSeccionBloqueCommand(idSeccionbloque: $id, idBloqueajustable: $request->integer('id_bloqueajustable') ?: null, titulo: $request->input('titulo'), contenido: $request->input('contenido')));
        return response()->json($this->showHandler->handle(new GetSeccionBloqueByIdQuery($id)));
    }
    public function destroy(int $id): JsonResponse {
        $this->deleteHandler->handle(new DeleteSeccionBloqueCommand($id));
        return response()->json(null, 204);
    }
}
