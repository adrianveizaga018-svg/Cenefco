<?php

namespace App\Http\Controllers\Api;

use App\Application\PlanesAcademicos\Commands\CreatePlanAcademicoCommand;
use App\Application\PlanesAcademicos\Commands\DeletePlanAcademicoCommand;
use App\Application\PlanesAcademicos\Commands\UpdatePlanAcademicoCommand;
use App\Application\PlanesAcademicos\Handlers\CreatePlanAcademicoHandler;
use App\Application\PlanesAcademicos\Handlers\DeletePlanAcademicoHandler;
use App\Application\PlanesAcademicos\Handlers\UpdatePlanAcademicoHandler;
use App\Application\PlanesAcademicos\Queries\GetPlanAcademicoByIdQuery;
use App\Application\PlanesAcademicos\Queries\GetPlanesAcademicosQuery;
use App\Application\PlanesAcademicos\QueryHandlers\GetPlanAcademicoByIdQueryHandler;
use App\Application\PlanesAcademicos\QueryHandlers\GetPlanesAcademicosQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\PlanesAcademicos\StorePlanAcademicoRequest;
use App\Http\Requests\PlanesAcademicos\UpdatePlanAcademicoRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanAcademicoController extends Controller
{
    public function __construct(
        private readonly GetPlanesAcademicosQueryHandler $getPlanesHandler,
        private readonly GetPlanAcademicoByIdQueryHandler $getPlanByIdHandler,
        private readonly CreatePlanAcademicoHandler $createHandler,
        private readonly UpdatePlanAcademicoHandler $updateHandler,
        private readonly DeletePlanAcademicoHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 30),
            'query'     => $request->get('query', ''),
            'sortKey'   => 'titulo',
            'sortOrder' => 'asc',
        ]);

        return response()->json(
            $this->getPlanesHandler->handle(new GetPlanesAcademicosQuery(
                pagination:   $pagination,
                conInactivos: $request->boolean('conInactivos', false),
                idCatplan:    $request->has('id_catplan') ? $request->integer('id_catplan') : null,
                idMat:        $request->has('id_mat') ? $request->integer('id_mat') : null,
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->getPlanByIdHandler->handle(new GetPlanAcademicoByIdQuery($id))
        );
    }

    public function store(StorePlanAcademicoRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreatePlanAcademicoCommand(
            id_plan:           $request->integer('id_plan'),
            id_us_reg:         $request->integer('id_us_reg', 0),
            titulo:            $request->titulo,
            titulo_plan:       $request->titulo_plan,
            convenio:          $request->convenio,
            convenio_id:       $request->filled('convenio_id') ? $request->integer('convenio_id') : null,
            anio:              $request->anio,
            numero_resolucion: $request->numero_resolucion,
            costo:             $request->costo,
            nro_cuotas:        $request->nro_cuotas,
            descuento:         $request->descuento,
            costo_por_cuota:   $request->costo_por_cuota,
            id_catplan:        $request->filled('id_catplan') ? $request->integer('id_catplan') : null,
            estado:            $request->integer('estado', 1),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdatePlanAcademicoRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdatePlanAcademicoCommand(
            id:                $id,
            titulo:            $request->titulo,
            titulo_plan:       $request->titulo_plan,
            convenio:          $request->convenio,
            convenio_id:       $request->filled('convenio_id') ? $request->integer('convenio_id') : null,
            anio:              $request->anio,
            numero_resolucion: $request->numero_resolucion,
            costo:             $request->costo,
            nro_cuotas:        $request->nro_cuotas,
            descuento:         $request->descuento,
            costo_por_cuota:   $request->costo_por_cuota,
            id_catplan:        $request->filled('id_catplan') ? $request->integer('id_catplan') : null,
            estado:            $request->filled('estado') ? $request->integer('estado') : null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeletePlanAcademicoCommand($id));

        return response()->json(null, 204);
    }
}
