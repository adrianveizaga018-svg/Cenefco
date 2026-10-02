<?php

namespace App\Http\Controllers\Api;

use App\Application\Secretarias\Commands\CreateSecretariaCommand;
use App\Application\Secretarias\Commands\DeleteSecretariaCommand;
use App\Application\Secretarias\Commands\UpdateSecretariaCommand;
use App\Application\Secretarias\Handlers\CreateSecretariaHandler;
use App\Application\Secretarias\Handlers\DeleteSecretariaHandler;
use App\Application\Secretarias\Handlers\UpdateSecretariaHandler;
use App\Application\Secretarias\Queries\GetSecretariaByIdQuery;
use App\Application\Secretarias\Queries\GetSecretariaBySlugQuery;
use App\Application\Secretarias\Queries\GetSecretariasQuery;
use App\Application\Secretarias\QueryHandlers\GetSecretariaByIdQueryHandler;
use App\Application\Secretarias\QueryHandlers\GetSecretariaBySlugQueryHandler;
use App\Application\Secretarias\QueryHandlers\GetSecretariasQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Secretarias\StoreSecretariaRequest;
use App\Http\Requests\Secretarias\UpdateSecretariaRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SecretariaController extends Controller
{
    public function __construct(
        private readonly GetSecretariasQueryHandler      $getSecretariasHandler,
        private readonly GetSecretariaByIdQueryHandler   $getSecretariaByIdHandler,
        private readonly GetSecretariaBySlugQueryHandler $getSecretariaBySlugHandler,
        private readonly CreateSecretariaHandler         $createHandler,
        private readonly UpdateSecretariaHandler         $updateHandler,
        private readonly DeleteSecretariaHandler         $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize' => $request->get('pageSize', 50),
            'query' => $request->get('query', ''),
            'sortKey' => $request->input('sort.key', 'orden_organigrama'),
            'sortOrder' => $request->input('sort.order', 'asc'),
        ]);

        return response()->json(
            $this->getSecretariasHandler->handle(
                new GetSecretariasQuery($pagination, $request->boolean('soloActivos', false))
            )
        );
    }

    public function store(StoreSecretariaRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateSecretariaCommand(
            nombre: $request->nombre,
            sigla: $request->sigla,
            atribuciones: $request->atribuciones,
            direccion_fisica: $request->direccion_fisica,
            telefono: $request->telefono,
            email: $request->email,
            horario_atencion: $request->horario_atencion,
            foto_titular_url: $request->foto_titular_url,
            orden_organigrama: $request->integer('orden_organigrama', 0),
            activa: $request->boolean('activa', true),
        ));

        return response()->json($dto, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->getSecretariaByIdHandler->handle(new GetSecretariaByIdQuery($id)));
    }

    public function showBySlug(string $slug): JsonResponse
    {
        return response()->json($this->getSecretariaBySlugHandler->handle(new GetSecretariaBySlugQuery($slug)));
    }

    public function update(UpdateSecretariaRequest $request, int $id): JsonResponse
    {
        return response()->json($this->updateHandler->handle(new UpdateSecretariaCommand($id, $request->validated())));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteSecretariaCommand($id));

        return response()->json(null, 204);
    }
}
