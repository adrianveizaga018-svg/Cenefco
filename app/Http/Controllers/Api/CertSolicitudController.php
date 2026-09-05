<?php

namespace App\Http\Controllers\Api;

use App\Application\CertConfigProgramas\Commands\AprobarSolicitudCommand;
use App\Application\CertConfigProgramas\Commands\RechazarSolicitudCommand;
use App\Application\CertConfigProgramas\Handlers\AprobarSolicitudHandler;
use App\Application\CertConfigProgramas\Handlers\RechazarSolicitudHandler;
use App\Application\CertConfigProgramas\Queries\GetSolicitudesPendientesCountQuery;
use App\Application\CertConfigProgramas\Queries\GetSolicitudesQuery;
use App\Application\CertConfigProgramas\QueryHandlers\GetSolicitudesPendientesCountQueryHandler;
use App\Application\CertConfigProgramas\QueryHandlers\GetSolicitudesQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\CertConfigProgramas\RechazarSolicitudRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CertSolicitudController extends Controller
{
    public function __construct(
        private readonly GetSolicitudesQueryHandler $getSolicitudesHandler,
        private readonly GetSolicitudesPendientesCountQueryHandler $getPendientesCountHandler,
        private readonly AprobarSolicitudHandler $aprobarHandler,
        private readonly RechazarSolicitudHandler $rechazarHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $result = $this->getSolicitudesHandler->handle(new GetSolicitudesQuery(
            pageSize:   $request->integer('pageSize', 15),
            page:       $request->integer('pageIndex', 1),
            estado:     $request->input('estado'),
            programa_id: $request->integer('programa_id') ?: null,
        ));
        return response()->json($result);
    }

    public function pendientesCount(): JsonResponse
    {
        $count = $this->getPendientesCountHandler->handle(new GetSolicitudesPendientesCountQuery());
        return response()->json(['pendientes' => $count]);
    }

    public function aprobar(int $id): JsonResponse
    {
        $dto = $this->aprobarHandler->handle(new AprobarSolicitudCommand($id));
        return response()->json($dto);
    }

    public function rechazar(RechazarSolicitudRequest $request, int $id): JsonResponse
    {
        $dto = $this->rechazarHandler->handle(new RechazarSolicitudCommand(
            solicitud_id: $id,
            nota_admin:   $request->input('nota_admin'),
        ));
        return response()->json($dto);
    }
}
