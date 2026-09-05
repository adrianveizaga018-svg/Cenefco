<?php

namespace App\Http\Controllers\Api;

use App\Application\CampanasLeads\Commands\CreateCampanaLeadCommand;
use App\Application\CampanasLeads\Commands\DeleteCampanaLeadCommand;
use App\Application\CampanasLeads\Commands\UpdateCampanaLeadCommand;
use App\Application\CampanasLeads\Handlers\CreateCampanaLeadHandler;
use App\Application\CampanasLeads\Handlers\DeleteCampanaLeadHandler;
use App\Application\CampanasLeads\Handlers\UpdateCampanaLeadHandler;
use App\Application\CampanasLeads\Queries\GetCampanaLeadByIdQuery;
use App\Application\CampanasLeads\Queries\GetCampanasLeadQuery;
use App\Application\CampanasLeads\QueryHandlers\GetCampanaLeadByIdQueryHandler;
use App\Application\CampanasLeads\QueryHandlers\GetCampanasLeadQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\CampanasLeads\StoreCampanaLeadRequest;
use App\Http\Requests\CampanasLeads\UpdateCampanaLeadRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CampanaLeadController extends Controller
{
    public function __construct(
        private readonly GetCampanasLeadQueryHandler $getCampanasHandler,
        private readonly GetCampanaLeadByIdQueryHandler $getByIdHandler,
        private readonly CreateCampanaLeadHandler $createHandler,
        private readonly UpdateCampanaLeadHandler $updateHandler,
        private readonly DeleteCampanaLeadHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 15),
            'query'     => $request->get('query', ''),
            'sortKey'   => $request->input('sort.key', 'created_at'),
            'sortOrder' => $request->input('sort.order', 'desc'),
        ]);

        return response()->json($this->getCampanasHandler->handle(
            new GetCampanasLeadQuery($pagination, $request->get('estado') ?: null)
        ));
    }

    public function store(StoreCampanaLeadRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateCampanaLeadCommand(
            nombre:       $request->nombre,
            descripcion:  $request->descripcion,
            estado:       $request->estado ?? 'activa',
            fecha_inicio: $request->fecha_inicio,
            fecha_fin:    $request->fecha_fin,
        ));

        return response()->json($dto, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->getByIdHandler->handle(new GetCampanaLeadByIdQuery($id)));
    }

    public function update(UpdateCampanaLeadRequest $request, int $id): JsonResponse
    {
        return response()->json($this->updateHandler->handle(new UpdateCampanaLeadCommand(
            id:           $id,
            nombre:       $request->nombre,
            descripcion:  $request->descripcion,
            estado:       $request->estado,
            fecha_inicio: $request->fecha_inicio,
            fecha_fin:    $request->fecha_fin,
        )));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteCampanaLeadCommand($id));

        return response()->json(null, 204);
    }
}
