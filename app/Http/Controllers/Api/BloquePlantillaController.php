<?php
namespace App\Http\Controllers\Api;
use App\Application\BloquesPlantilla\Commands\CreateBloquePlantillaCommand;
use App\Application\BloquesPlantilla\Commands\DeleteBloquePlantillaCommand;
use App\Application\BloquesPlantilla\Commands\UpdateBloquePlantillaCommand;
use App\Application\BloquesPlantilla\Handlers\CreateBloquePlantillaHandler;
use App\Application\BloquesPlantilla\Handlers\DeleteBloquePlantillaHandler;
use App\Application\BloquesPlantilla\Handlers\UpdateBloquePlantillaHandler;
use App\Application\BloquesPlantilla\Queries\GetBloquePlantillaByIdQuery;
use App\Application\BloquesPlantilla\Queries\GetBloquesPlantillaQuery;
use App\Application\BloquesPlantilla\QueryHandlers\GetBloquePlantillaByIdQueryHandler;
use App\Application\BloquesPlantilla\QueryHandlers\GetBloquesPlantillaQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\BloquesPlantilla\StoreBloquePlantillaRequest;
use App\Http\Requests\BloquesPlantilla\UpdateBloquePlantillaRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class BloquePlantillaController extends Controller {
    public function __construct(
        private readonly GetBloquesPlantillaQueryHandler $listHandler,
        private readonly GetBloquePlantillaByIdQueryHandler $showHandler,
        private readonly CreateBloquePlantillaHandler $createHandler,
        private readonly UpdateBloquePlantillaHandler $updateHandler,
        private readonly DeleteBloquePlantillaHandler $deleteHandler,
    ) {}
    public function index(Request $request): JsonResponse {
        $pagination = PaginationDTO::fromArray(['pageIndex' => $request->get('pageIndex', 1), 'pageSize' => $request->get('pageSize', 15)]);
        return response()->json($this->listHandler->handle(new GetBloquesPlantillaQuery(pagination: $pagination, conInactivos: $request->boolean('con_inactivos'))));
    }
    public function show(int $id): JsonResponse {
        return response()->json($this->showHandler->handle(new GetBloquePlantillaByIdQuery($id)));
    }
    public function store(StoreBloquePlantillaRequest $request): JsonResponse {
        $dto = $this->createHandler->handle(new CreateBloquePlantillaCommand(idBloqueplantilla: $request->integer('id_bloqueplantilla'), nombre: $request->input('nombre'), descripcion: $request->input('descripcion'), idUsReg: auth()->id()));
        return response()->json($dto, 201);
    }
    public function update(UpdateBloquePlantillaRequest $request, int $id): JsonResponse {
        $this->updateHandler->handle(new UpdateBloquePlantillaCommand(idBloqueplantilla: $id, nombre: $request->input('nombre'), descripcion: $request->input('descripcion')));
        return response()->json($this->showHandler->handle(new GetBloquePlantillaByIdQuery($id)));
    }
    public function destroy(int $id): JsonResponse {
        $this->deleteHandler->handle(new DeleteBloquePlantillaCommand($id));
        return response()->json(null, 204);
    }
}
