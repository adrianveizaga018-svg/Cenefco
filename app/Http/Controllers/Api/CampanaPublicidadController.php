<?php

namespace App\Http\Controllers\Api;

use App\Application\CampanasPublicidad\Commands\CreateCampanaPublicidadCommand;
use App\Application\CampanasPublicidad\Commands\DeleteCampanaPublicidadCommand;
use App\Application\CampanasPublicidad\Commands\RegistrarMetricaCampanaCommand;
use App\Application\CampanasPublicidad\Commands\UpdateCampanaPublicidadCommand;
use App\Application\CampanasPublicidad\Handlers\CreateCampanaPublicidadHandler;
use App\Application\CampanasPublicidad\Handlers\DeleteCampanaPublicidadHandler;
use App\Application\CampanasPublicidad\Handlers\RegistrarMetricaCampanaHandler;
use App\Application\CampanasPublicidad\Handlers\UpdateCampanaPublicidadHandler;
use App\Application\CampanasPublicidad\Queries\GetCampanaByIdQuery;
use App\Application\CampanasPublicidad\Queries\GetCampanasPublicidadQuery;
use App\Application\CampanasPublicidad\Queries\GetReporteCampanasQuery;
use App\Application\CampanasPublicidad\QueryHandlers\GetCampanaByIdQueryHandler;
use App\Application\CampanasPublicidad\QueryHandlers\GetCampanasPublicidadQueryHandler;
use App\Application\CampanasPublicidad\QueryHandlers\GetReporteCampanasQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\CampanasPublicidad\StoreCampanaPublicidadRequest;
use App\Http\Requests\CampanasPublicidad\StoreMetricaCampanaRequest;
use App\Http\Requests\CampanasPublicidad\UpdateCampanaPublicidadRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Campañas Publicitarias')]
class CampanaPublicidadController extends Controller
{
    public function __construct(
        private readonly GetCampanasPublicidadQueryHandler $getListHandler,
        private readonly GetCampanaByIdQueryHandler         $getByIdHandler,
        private readonly GetReporteCampanasQueryHandler     $getReporteHandler,
        private readonly CreateCampanaPublicidadHandler     $createHandler,
        private readonly UpdateCampanaPublicidadHandler     $updateHandler,
        private readonly DeleteCampanaPublicidadHandler     $deleteHandler,
        private readonly RegistrarMetricaCampanaHandler     $registrarMetricaHandler,
    ) {}

    #[OA\Get(path: '/api/v1/campanas-publicidad', summary: 'Listar campañas publicitarias', tags: ['Campañas Publicitarias'])]
    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 15),
            'query'     => $request->get('query', ''),
            'sortKey'   => $request->get('sortKey', 'fecha_inicio'),
            'sortOrder' => $request->get('sortOrder', 'desc'),
        ], 'fecha_inicio');

        $filtros = $request->only(['programa_id', 'plataforma', 'estado', 'fecha_desde', 'fecha_hasta']);

        return response()->json($this->getListHandler->handle(new GetCampanasPublicidadQuery($pagination, $filtros)));
    }

    #[OA\Get(path: '/api/v1/campanas-publicidad/reporte', summary: 'Reporte de inversión por curso/campaña', tags: ['Campañas Publicitarias'])]
    public function reporte(Request $request): JsonResponse
    {
        return response()->json($this->getReporteHandler->handle(new GetReporteCampanasQuery(
            fechaInicio: $request->get('fecha_desde'),
            fechaFin:    $request->get('fecha_hasta'),
        )));
    }

    #[OA\Get(path: '/api/v1/campanas-publicidad/{id}', summary: 'Detalle de campaña (con métricas y gasto vinculado)', tags: ['Campañas Publicitarias'])]
    public function show(int $id): JsonResponse
    {
        return response()->json($this->getByIdHandler->handle(new GetCampanaByIdQuery($id)));
    }

    #[OA\Post(path: '/api/v1/campanas-publicidad', summary: 'Crear campaña publicitaria', tags: ['Campañas Publicitarias'])]
    public function store(StoreCampanaPublicidadRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateCampanaPublicidadCommand(
            nombre:                  $request->nombre,
            plataforma:              $request->plataforma,
            fecha_inicio:            $request->fecha_inicio,
            programa_id:             $request->programa_id ? (int) $request->programa_id : null,
            proposito:               $request->input('proposito', 'curso'),
            objetivo:                $request->objetivo,
            fecha_fin:               $request->fecha_fin,
            estado:                  $request->input('estado', 'planificada'),
            leads:                   $request->filled('leads') ? (int) $request->leads : null,
            presupuesto_usd:         $request->filled('presupuesto_usd') ? (float) $request->presupuesto_usd : null,
            presupuesto_bob:         $request->filled('presupuesto_bob') ? (float) $request->presupuesto_bob : null,
            id_campana_externa:      $request->id_campana_externa,
            responsable:             $request->responsable,
            notas:                   $request->notas,
        ));

        return response()->json($dto, 201);
    }

    #[OA\Put(path: '/api/v1/campanas-publicidad/{id}', summary: 'Actualizar campaña publicitaria', tags: ['Campañas Publicitarias'])]
    public function update(UpdateCampanaPublicidadRequest $request, int $id): JsonResponse
    {
        return response()->json($this->updateHandler->handle(new UpdateCampanaPublicidadCommand(
            id:                      $id,
            programa_id:             $request->filled('programa_id') ? (int) $request->programa_id : null,
            proposito:               $request->proposito,
            nombre:                  $request->nombre,
            plataforma:              $request->plataforma,
            objetivo:                $request->objetivo,
            fecha_inicio:            $request->fecha_inicio,
            fecha_fin:               $request->fecha_fin,
            estado:                  $request->estado,
            leads:                   $request->filled('leads') ? (int) $request->leads : null,
            presupuesto_usd:         $request->filled('presupuesto_usd') ? (float) $request->presupuesto_usd : null,
            presupuesto_bob:         $request->filled('presupuesto_bob') ? (float) $request->presupuesto_bob : null,
            id_campana_externa:      $request->id_campana_externa,
            responsable:             $request->responsable,
            notas:                   $request->notas,
        )));
    }

    #[OA\Delete(path: '/api/v1/campanas-publicidad/{id}', summary: 'Eliminar campaña publicitaria', tags: ['Campañas Publicitarias'])]
    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteCampanaPublicidadCommand($id));

        return response()->json(null, 204);
    }

    #[OA\Post(path: '/api/v1/campanas-publicidad/{id}/metricas', summary: 'Registrar un corte de métricas (reporte de Meta/Google Ads)', tags: ['Campañas Publicitarias'])]
    public function registrarMetrica(StoreMetricaCampanaRequest $request, int $id): JsonResponse
    {
        $dto = $this->registrarMetricaHandler->handle(new RegistrarMetricaCampanaCommand(
            campana_publicidad_id: $id,
            fecha_corte:           $request->fecha_corte,
            alcance:               $request->filled('alcance') ? (int) $request->alcance : null,
            impresiones:           $request->filled('impresiones') ? (int) $request->impresiones : null,
            frecuencia:            $request->filled('frecuencia') ? (float) $request->frecuencia : null,
            clics_enlace:          $request->filled('clics_enlace') ? (int) $request->clics_enlace : null,
            ctr:                   $request->filled('ctr') ? (float) $request->ctr : null,
            cpc:                   $request->filled('cpc') ? (float) $request->cpc : null,
            cpm:                   $request->filled('cpm') ? (float) $request->cpm : null,
            resultados:            $request->filled('resultados') ? (int) $request->resultados : null,
            tipo_resultado:        $request->tipo_resultado,
            costo_por_resultado:   $request->filled('costo_por_resultado') ? (float) $request->costo_por_resultado : null,
            gasto_periodo:         $request->filled('gasto_periodo') ? (float) $request->gasto_periodo : null,
            fuente:                $request->input('fuente', 'manual'),
            notas:                 $request->notas,
        ));

        return response()->json($dto, 201);
    }
}
