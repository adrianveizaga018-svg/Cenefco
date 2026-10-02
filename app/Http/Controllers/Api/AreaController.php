<?php

namespace App\Http\Controllers\Api;

use App\Application\Areas\Commands\CreateAreaCommand;
use App\Application\Areas\Commands\DeleteAreaCommand;
use App\Application\Areas\Commands\UpdateAreaCommand;
use App\Application\Areas\Handlers\CreateAreaHandler;
use App\Application\Areas\Handlers\DeleteAreaHandler;
use App\Application\Areas\Handlers\UpdateAreaHandler;
use App\Application\Areas\Queries\GetAreaByIdQuery;
use App\Application\Areas\Queries\GetAreaBySlugQuery;
use App\Application\Areas\Queries\GetAreaConCursosQuery;
use App\Application\Areas\Queries\GetAreasActivasQuery;
use App\Application\Areas\Queries\GetAreasQuery;
use App\Application\Areas\QueryHandlers\GetAreaByIdQueryHandler;
use App\Application\Areas\QueryHandlers\GetAreaBySlugQueryHandler;
use App\Application\Areas\QueryHandlers\GetAreaConCursosQueryHandler;
use App\Application\Areas\QueryHandlers\GetAreasActivasQueryHandler;
use App\Application\Areas\QueryHandlers\GetAreasQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Areas\StoreAreaRequest;
use App\Http\Requests\Areas\UpdateAreaRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function __construct(
        private readonly GetAreasQueryHandler        $getAreasHandler,
        private readonly GetAreaByIdQueryHandler     $getByIdHandler,
        private readonly GetAreaBySlugQueryHandler   $getBySlugHandler,
        private readonly GetAreasActivasQueryHandler $getAreasActivasHandler,
        private readonly GetAreaConCursosQueryHandler $getAreaConCursosHandler,
        private readonly CreateAreaHandler           $createHandler,
        private readonly UpdateAreaHandler           $updateHandler,
        private readonly DeleteAreaHandler           $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 20),
            'query'     => $request->get('query', ''),
        ]);

        return response()->json(
            $this->getAreasHandler->handle(new GetAreasQuery($pagination))
        );
    }

    public function indexPublico(): JsonResponse
    {
        return response()->json(
            $this->getAreasActivasHandler->handle(new GetAreasActivasQuery())
        );
    }

    public function showPublico(string $slug): JsonResponse
    {
        return response()->json(
            $this->getAreaConCursosHandler->handle(new GetAreaConCursosQuery($slug))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->getByIdHandler->handle(new GetAreaByIdQuery($id))
        );
    }

    public function showBySlug(string $slug): JsonResponse
    {
        return response()->json(
            $this->getBySlugHandler->handle(new GetAreaBySlugQuery($slug))
        );
    }

    public function store(StoreAreaRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateAreaCommand(
            titulo:           $request->titulo,
            slug:             $request->slug,
            descripcion:      $request->descripcion,
            logo_url:         $request->logo_url,
            logo_alt:         $request->logo_alt,
            galeria:          $request->galeria ?? [],
            color:            $request->color,
            icono:            $request->icono,
            orden:            $request->integer('orden', 0),
            activo:           $request->boolean('activo', true),
            meta_titulo:      $request->meta_titulo,
            meta_descripcion: $request->meta_descripcion,
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateAreaRequest $request, int $id): JsonResponse
    {
        return response()->json(
            $this->updateHandler->handle(new UpdateAreaCommand(
                id:   $id,
                data: $request->validated(),
            ))
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteAreaCommand($id));

        return response()->json(null, 204);
    }
}
