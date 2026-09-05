<?php

namespace App\Http\Controllers\Api;

use App\Application\UsuariosPlanDoc\Commands\CreateUsuarioPlanDocCommand;
use App\Application\UsuariosPlanDoc\Commands\DeleteUsuarioPlanDocCommand;
use App\Application\UsuariosPlanDoc\Commands\UpdateUsuarioPlanDocCommand;
use App\Application\UsuariosPlanDoc\Handlers\CreateUsuarioPlanDocHandler;
use App\Application\UsuariosPlanDoc\Handlers\DeleteUsuarioPlanDocHandler;
use App\Application\UsuariosPlanDoc\Handlers\UpdateUsuarioPlanDocHandler;
use App\Application\UsuariosPlanDoc\Queries\GetUsuarioPlanDocByIdQuery;
use App\Application\UsuariosPlanDoc\Queries\GetUsuariosPlanDocQuery;
use App\Application\UsuariosPlanDoc\QueryHandlers\GetUsuarioPlanDocByIdQueryHandler;
use App\Application\UsuariosPlanDoc\QueryHandlers\GetUsuariosPlanDocQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\UsuariosPlanDoc\StoreUsuarioPlanDocRequest;
use App\Http\Requests\UsuariosPlanDoc\UpdateUsuarioPlanDocRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsuarioPlanDocController extends Controller
{
    public function __construct(
        private readonly GetUsuariosPlanDocQueryHandler    $listHandler,
        private readonly GetUsuarioPlanDocByIdQueryHandler $showHandler,
        private readonly CreateUsuarioPlanDocHandler       $createHandler,
        private readonly UpdateUsuarioPlanDocHandler       $updateHandler,
        private readonly DeleteUsuarioPlanDocHandler       $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 50),
        ]);

        return response()->json(
            $this->listHandler->handle(new GetUsuariosPlanDocQuery(
                pagination:   $pagination,
                idUs:         $request->filled('id_us') ? (int) $request->get('id_us') : null,
                idPlanDoc:    $request->filled('id_plandoc') ? (int) $request->get('id_plandoc') : null,
                conInactivos: $request->boolean('conInactivos', false),
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->showHandler->handle(new GetUsuarioPlanDocByIdQuery($id))
        );
    }

    public function store(StoreUsuarioPlanDocRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateUsuarioPlanDocCommand(
            id_usuarioplandoc:  $request->integer('id_usuarioplandoc'),
            id_us:              $request->integer('id_us'),
            id_us_reg:          $request->integer('id_us_reg', 0),
            num_usuarioplandoc: $request->integer('num_usuarioplandoc', 0),
            id_plandoc:         $request->filled('id_plandoc') ? $request->integer('id_plandoc') : null,
            estado:             $request->integer('estado', 1),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateUsuarioPlanDocRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateUsuarioPlanDocCommand(
            id:         $id,
            id_plandoc: $request->filled('id_plandoc') ? $request->integer('id_plandoc') : null,
            estado:     $request->filled('estado') ? $request->integer('estado') : null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteUsuarioPlanDocCommand($id));

        return response()->json(null, 204);
    }
}
