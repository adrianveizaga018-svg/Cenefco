<?php

namespace App\Http\Controllers\WebMenus;

use App\Application\WebMenus\Commands\CreateWebMenuCommand;
use App\Application\WebMenus\Commands\DeleteWebMenuCommand;
use App\Application\WebMenus\Commands\UpdateWebMenuCommand;
use App\Application\WebMenus\Handlers\CreateWebMenuHandler;
use App\Application\WebMenus\Handlers\DeleteWebMenuHandler;
use App\Application\WebMenus\Handlers\UpdateWebMenuHandler;
use App\Application\WebMenus\Queries\GetWebMenuByIdQuery;
use App\Application\WebMenus\Queries\GetWebMenusQuery;
use App\Application\WebMenus\QueryHandlers\GetWebMenuByIdQueryHandler;
use App\Application\WebMenus\QueryHandlers\GetWebMenusQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\WebMenus\StoreWebMenuRequest;
use App\Http\Requests\WebMenus\UpdateWebMenuRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebMenuController extends Controller
{
    public function __construct(
        private readonly GetWebMenusQueryHandler $listHandler,
        private readonly GetWebMenuByIdQueryHandler $showHandler,
        private readonly CreateWebMenuHandler $createHandler,
        private readonly UpdateWebMenuHandler $updateHandler,
        private readonly DeleteWebMenuHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 15),
        ]);

        return response()->json($this->listHandler->handle(new GetWebMenusQuery(
            pagination: $pagination,
        )));
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->showHandler->handle(new GetWebMenuByIdQuery($id)));
    }

    public function store(StoreWebMenuRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateWebMenuCommand(
            nombre:      $request->string('nombre')->toString(),
            descripcion: $request->input('descripcion'),
            activo:      $request->boolean('activo', true),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateWebMenuRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateWebMenuCommand(
            id:          $id,
            nombre:      $request->input('nombre'),
            descripcion: $request->input('descripcion'),
            activo:      $request->has('activo') ? $request->boolean('activo') : null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteWebMenuCommand($id));

        return response()->json(null, 204);
    }
}
