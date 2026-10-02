<?php
namespace App\Http\Controllers\Api;
use App\Application\ConfiguracionesAcademicas\Commands\CreateConfiguracionAcademicaCommand;
use App\Application\ConfiguracionesAcademicas\Commands\DeleteConfiguracionAcademicaCommand;
use App\Application\ConfiguracionesAcademicas\Commands\UpdateConfiguracionAcademicaCommand;
use App\Application\ConfiguracionesAcademicas\Handlers\CreateConfiguracionAcademicaHandler;
use App\Application\ConfiguracionesAcademicas\Handlers\DeleteConfiguracionAcademicaHandler;
use App\Application\ConfiguracionesAcademicas\Handlers\UpdateConfiguracionAcademicaHandler;
use App\Application\ConfiguracionesAcademicas\Queries\GetConfiguracionAcademicaByIdQuery;
use App\Application\ConfiguracionesAcademicas\Queries\GetConfiguracionesAcademicasQuery;
use App\Application\ConfiguracionesAcademicas\QueryHandlers\GetConfiguracionAcademicaByIdQueryHandler;
use App\Application\ConfiguracionesAcademicas\QueryHandlers\GetConfiguracionesAcademicasQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\ConfiguracionesAcademicas\StoreConfiguracionAcademicaRequest;
use App\Http\Requests\ConfiguracionesAcademicas\UpdateConfiguracionAcademicaRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class ConfiguracionAcademicaController extends Controller {
    public function __construct(
        private readonly GetConfiguracionesAcademicasQueryHandler $listHandler,
        private readonly GetConfiguracionAcademicaByIdQueryHandler $showHandler,
        private readonly CreateConfiguracionAcademicaHandler $createHandler,
        private readonly UpdateConfiguracionAcademicaHandler $updateHandler,
        private readonly DeleteConfiguracionAcademicaHandler $deleteHandler,
    ) {}
    public function index(Request $request): JsonResponse {
        $pagination = PaginationDTO::fromArray(['pageIndex' => $request->get('pageIndex', 1), 'pageSize' => $request->get('pageSize', 15)]);
        return response()->json($this->listHandler->handle(new GetConfiguracionesAcademicasQuery(
            pagination: $pagination,
            gestion: $request->integer('gestion') ?: null,
            idPlan: $request->integer('id_plan') ?: null,
            conInactivos: $request->boolean('con_inactivos'),
        )));
    }
    public function show(int $id): JsonResponse {
        return response()->json($this->showHandler->handle(new GetConfiguracionAcademicaByIdQuery($id)));
    }
    public function store(StoreConfiguracionAcademicaRequest $request): JsonResponse {
        $dto = $this->createHandler->handle(new CreateConfiguracionAcademicaCommand(
            idConf: $request->integer('id_conf'),
            gestion: $request->integer('gestion'),
            idPlan: $request->integer('id_plan'),
            descripcion: $request->input('descripcion'),
            idUsReg: auth()->id(),
        ));
        return response()->json($dto, 201);
    }
    public function update(UpdateConfiguracionAcademicaRequest $request, int $id): JsonResponse {
        $this->updateHandler->handle(new UpdateConfiguracionAcademicaCommand(
            idConf: $id,
            gestion: $request->integer('gestion') ?: null,
            idPlan: $request->integer('id_plan') ?: null,
            descripcion: $request->input('descripcion'),
        ));
        return response()->json($this->showHandler->handle(new GetConfiguracionAcademicaByIdQuery($id)));
    }
    public function destroy(int $id): JsonResponse {
        $this->deleteHandler->handle(new DeleteConfiguracionAcademicaCommand($id));
        return response()->json(null, 204);
    }
}
