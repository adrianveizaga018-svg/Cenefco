<?php

namespace App\Http\Controllers\WebMenuItems;

use App\Application\WebMenuItems\Commands\CreateWebMenuItemCommand;
use App\Application\WebMenuItems\Commands\DeleteWebMenuItemCommand;
use App\Application\WebMenuItems\Commands\UpdateWebMenuItemCommand;
use App\Application\WebMenuItems\Handlers\CreateWebMenuItemHandler;
use App\Application\WebMenuItems\Handlers\DeleteWebMenuItemHandler;
use App\Application\WebMenuItems\Handlers\UpdateWebMenuItemHandler;
use App\Application\WebMenuItems\Queries\GetWebMenuItemByIdQuery;
use App\Application\WebMenuItems\Queries\GetWebMenuItemsQuery;
use App\Application\WebMenuItems\QueryHandlers\GetWebMenuItemByIdQueryHandler;
use App\Application\WebMenuItems\QueryHandlers\GetWebMenuItemsQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\WebMenuItems\StoreWebMenuItemRequest;
use App\Http\Requests\WebMenuItems\UpdateWebMenuItemRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebMenuItemController extends Controller
{
    public function __construct(
        private readonly GetWebMenuItemsQueryHandler $listHandler,
        private readonly GetWebMenuItemByIdQueryHandler $showHandler,
        private readonly CreateWebMenuItemHandler $createHandler,
        private readonly UpdateWebMenuItemHandler $updateHandler,
        private readonly DeleteWebMenuItemHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 15),
        ]);

        return response()->json($this->listHandler->handle(new GetWebMenuItemsQuery(
            pagination: $pagination,
            menuId:     $request->integer('menu_id') ?: null,
        )));
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->showHandler->handle(new GetWebMenuItemByIdQuery($id)));
    }

    public function store(StoreWebMenuItemRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateWebMenuItemCommand(
            menuId: $request->integer('menu_id'),
            label:  $request->string('label')->toString(),
            url:    $request->string('url')->toString(),
            orden:  $request->integer('orden') ?: null,
            target: $request->input('target'),
            activo: $request->boolean('activo', true),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateWebMenuItemRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateWebMenuItemCommand(
            id:     $id,
            menuId: $request->integer('menu_id') ?: null,
            label:  $request->input('label'),
            url:    $request->input('url'),
            orden:  $request->integer('orden') ?: null,
            target: $request->input('target'),
            activo: $request->has('activo') ? $request->boolean('activo') : null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteWebMenuItemCommand($id));

        return response()->json(null, 204);
    }
}
