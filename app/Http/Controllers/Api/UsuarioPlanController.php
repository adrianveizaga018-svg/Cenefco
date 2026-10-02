<?php

namespace App\Http\Controllers\Api;

use App\Application\UsuariosPlan\Commands\CreateUsuarioPlanCommand;
use App\Application\UsuariosPlan\Commands\DeleteUsuarioPlanCommand;
use App\Application\UsuariosPlan\Commands\UpdateUsuarioPlanCommand;
use App\Application\UsuariosPlan\Handlers\CreateUsuarioPlanHandler;
use App\Application\UsuariosPlan\Handlers\DeleteUsuarioPlanHandler;
use App\Application\UsuariosPlan\Handlers\UpdateUsuarioPlanHandler;
use App\Application\UsuariosPlan\Queries\GetUsuarioPlanByIdQuery;
use App\Application\UsuariosPlan\Queries\GetUsuariosPlanQuery;
use App\Application\UsuariosPlan\QueryHandlers\GetUsuarioPlanByIdQueryHandler;
use App\Application\UsuariosPlan\QueryHandlers\GetUsuariosPlanQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\UsuariosPlan\StoreUsuarioPlanRequest;
use App\Http\Requests\UsuariosPlan\UpdateUsuarioPlanRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsuarioPlanController extends Controller
{
    public function __construct(
        private readonly GetUsuariosPlanQueryHandler    $listHandler,
        private readonly GetUsuarioPlanByIdQueryHandler $showHandler,
        private readonly CreateUsuarioPlanHandler       $createHandler,
        private readonly UpdateUsuarioPlanHandler       $updateHandler,
        private readonly DeleteUsuarioPlanHandler       $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 50),
        ]);

        return response()->json(
            $this->listHandler->handle(new GetUsuariosPlanQuery(
                pagination:   $pagination,
                idUs:         $request->filled('id_us') ? (int) $request->get('id_us') : null,
                idPlan:       $request->filled('id_plan') ? (int) $request->get('id_plan') : null,
                conInactivos: $request->boolean('conInactivos', false),
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->showHandler->handle(new GetUsuarioPlanByIdQuery($id))
        );
    }

    public function store(StoreUsuarioPlanRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateUsuarioPlanCommand(
            id_usuarioplan:  $request->integer('id_usuarioplan'),
            id_us:           $request->integer('id_us'),
            id_us_reg:       $request->integer('id_us_reg', 0),
            num_usuarioplan: $request->integer('num_usuarioplan', 0),
            id_plan:         $request->filled('id_plan') ? $request->integer('id_plan') : null,
            estado:          $request->integer('estado', 1),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateUsuarioPlanRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateUsuarioPlanCommand(
            id:      $id,
            id_plan: $request->filled('id_plan') ? $request->integer('id_plan') : null,
            estado:  $request->filled('estado') ? $request->integer('estado') : null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteUsuarioPlanCommand($id));

        return response()->json(null, 204);
    }
}
