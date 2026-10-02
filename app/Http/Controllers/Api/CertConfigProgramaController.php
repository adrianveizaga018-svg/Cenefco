<?php

namespace App\Http\Controllers\Api;

use App\Application\CertConfigProgramas\Commands\CreateCertConfigItemCommand;
use App\Application\CertConfigProgramas\Commands\DeleteCertConfigCommand;
use App\Application\CertConfigProgramas\Commands\DeleteCertConfigItemCommand;
use App\Application\CertConfigProgramas\Commands\ToggleCertConfigCommand;
use App\Application\CertConfigProgramas\Commands\UpdateCertConfigItemCommand;
use App\Application\CertConfigProgramas\Commands\UpsertCertConfigCommand;
use App\Application\CertConfigProgramas\Handlers\CreateCertConfigItemHandler;
use App\Application\CertConfigProgramas\Handlers\DeleteCertConfigHandler;
use App\Application\CertConfigProgramas\Handlers\DeleteCertConfigItemHandler;
use App\Application\CertConfigProgramas\Handlers\ToggleCertConfigHandler;
use App\Application\CertConfigProgramas\Handlers\UpdateCertConfigItemHandler;
use App\Application\CertConfigProgramas\Handlers\UpsertCertConfigHandler;
use App\Application\CertConfigProgramas\Queries\GetCertConfigByProgramaQuery;
use App\Application\CertConfigProgramas\Queries\GetCertConfigsQuery;
use App\Application\CertConfigProgramas\QueryHandlers\GetCertConfigByProgramaQueryHandler;
use App\Application\CertConfigProgramas\QueryHandlers\GetCertConfigsQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\CertConfigProgramas\StoreCertConfigItemRequest;
use App\Http\Requests\CertConfigProgramas\UpdateCertConfigItemRequest;
use App\Http\Requests\CertConfigProgramas\UpsertCertConfigRequest;
use Illuminate\Http\JsonResponse;

class CertConfigProgramaController extends Controller
{
    public function __construct(
        private readonly GetCertConfigsQueryHandler $getConfigsHandler,
        private readonly GetCertConfigByProgramaQueryHandler $getByProgramaHandler,
        private readonly UpsertCertConfigHandler $upsertHandler,
        private readonly ToggleCertConfigHandler $toggleHandler,
        private readonly CreateCertConfigItemHandler $createItemHandler,
        private readonly UpdateCertConfigItemHandler $updateItemHandler,
        private readonly DeleteCertConfigItemHandler $deleteItemHandler,
        private readonly DeleteCertConfigHandler $deleteConfigHandler,
    ) {}

    public function index(): JsonResponse
    {
        $configs = $this->getConfigsHandler->handle(new GetCertConfigsQuery());
        return response()->json(['data' => $configs, 'total' => count($configs)]);
    }

    public function showByPrograma(int $programaId): JsonResponse
    {
        $config = $this->getByProgramaHandler->handle(new GetCertConfigByProgramaQuery($programaId));
        if (! $config) {
            return response()->json(['message' => 'No hay configuración activa para este programa.'], 404);
        }
        return response()->json($config);
    }

    public function upsert(UpsertCertConfigRequest $request): JsonResponse
    {
        $dto = $this->upsertHandler->handle(new UpsertCertConfigCommand(
            programa_id:  $request->integer('programa_id'),
            activo:       $request->boolean('activo', true),
            titulo:       $request->input('titulo'),
            descripcion:  $request->input('descripcion'),
        ));
        return response()->json($dto);
    }

    public function toggle(int $id): JsonResponse
    {
        $dto = $this->toggleHandler->handle(new ToggleCertConfigCommand($id));
        return response()->json($dto);
    }

    public function storeItem(StoreCertConfigItemRequest $request, int $configId): JsonResponse
    {
        $dto = $this->createItemHandler->handle(new CreateCertConfigItemCommand(
            config_id:    $configId,
            plantilla_id: $request->integer('plantilla_id') ?: null,
            nombre_cert:  $request->input('nombre_cert'),
            precio:       (float) $request->input('precio', 0),
            es_gratuito:  $request->boolean('es_gratuito', false),
            orden:        $request->integer('orden', 0),
        ));
        return response()->json($dto, 201);
    }

    public function updateItem(UpdateCertConfigItemRequest $request, int $configId, int $itemId): JsonResponse
    {
        $dto = $this->updateItemHandler->handle(new UpdateCertConfigItemCommand(
            id:           $itemId,
            plantilla_id: $request->integer('plantilla_id') ?: null,
            nombre_cert:  $request->input('nombre_cert'),
            precio:       (float) $request->input('precio', 0),
            es_gratuito:  $request->boolean('es_gratuito', false),
            orden:        $request->integer('orden', 0),
            activo:       $request->boolean('activo', true),
        ));
        return response()->json($dto);
    }

    public function destroyItem(int $configId, int $itemId): JsonResponse
    {
        $this->deleteItemHandler->handle(new DeleteCertConfigItemCommand($itemId));
        return response()->json(null, 204);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteConfigHandler->handle(new DeleteCertConfigCommand($id));
        return response()->json(null, 204);
    }
}
