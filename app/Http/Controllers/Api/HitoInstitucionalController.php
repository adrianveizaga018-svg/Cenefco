<?php

namespace App\Http\Controllers\Api;

use App\Application\HitosInstitucionales\Commands\CreateHitoInstitucionalCommand;
use App\Application\HitosInstitucionales\Commands\DeleteHitoInstitucionalCommand;
use App\Application\HitosInstitucionales\Commands\UpdateHitoInstitucionalCommand;
use App\Application\HitosInstitucionales\Handlers\CreateHitoInstitucionalHandler;
use App\Application\HitosInstitucionales\Handlers\DeleteHitoInstitucionalHandler;
use App\Application\HitosInstitucionales\Handlers\UpdateHitoInstitucionalHandler;
use App\Application\HitosInstitucionales\Queries\GetHitoInstitucionalByIdQuery;
use App\Application\HitosInstitucionales\Queries\GetHitosInstitucionalQuery;
use App\Application\HitosInstitucionales\QueryHandlers\GetHitoInstitucionalByIdQueryHandler;
use App\Application\HitosInstitucionales\QueryHandlers\GetHitosInstitucionalQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\HitosInstitucionales\StoreHitoInstitucionalRequest;
use App\Http\Requests\HitosInstitucionales\UpdateHitoInstitucionalRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HitoInstitucionalController extends Controller
{
    public function __construct(
        private readonly GetHitosInstitucionalQueryHandler $getHitosHandler,
        private readonly GetHitoInstitucionalByIdQueryHandler $getByIdHandler,
        private readonly CreateHitoInstitucionalHandler $createHandler,
        private readonly UpdateHitoInstitucionalHandler $updateHandler,
        private readonly DeleteHitoInstitucionalHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 15),
            'query'     => $request->get('query', ''),
            'sortKey'   => $request->input('sort.key', 'orden'),
            'sortOrder' => $request->input('sort.order', 'asc'),
        ]);

        return response()->json($this->getHitosHandler->handle(new GetHitosInstitucionalQuery(
            $pagination,
            $request->boolean('soloActivos', false),
        )));
    }

    public function store(StoreHitoInstitucionalRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateHitoInstitucionalCommand(
            anio:        $request->anio,
            titulo:      $request->titulo,
            descripcion: $request->descripcion,
            imagen_url:  $request->imagen_url,
            imagen_alt:  $request->imagen_alt,
            orden:       $request->integer('orden', 0),
            activo:      $request->boolean('activo', true),
        ));

        return response()->json($dto, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->getByIdHandler->handle(new GetHitoInstitucionalByIdQuery($id)));
    }

    public function update(UpdateHitoInstitucionalRequest $request, int $id): JsonResponse
    {
        return response()->json($this->updateHandler->handle(new UpdateHitoInstitucionalCommand(
            id:          $id,
            anio:        $request->anio,
            titulo:      $request->titulo,
            descripcion: $request->descripcion,
            imagen_url:  $request->imagen_url,
            imagen_alt:  $request->imagen_alt,
            orden:       $request->has('orden') ? $request->integer('orden') : null,
            activo:      $request->has('activo') ? $request->boolean('activo') : null,
        )));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteHitoInstitucionalCommand($id));

        return response()->json(null, 204);
    }
}
