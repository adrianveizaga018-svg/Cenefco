<?php

namespace App\Http\Controllers\Api;

use App\Application\CampanasLeads\Commands\CreateLeadCommand;
use App\Application\CampanasLeads\Commands\DeleteLeadCommand;
use App\Application\CampanasLeads\Commands\UpdateLeadCommand;
use App\Application\CampanasLeads\Handlers\CreateLeadHandler;
use App\Application\CampanasLeads\Handlers\DeleteLeadHandler;
use App\Application\CampanasLeads\Handlers\UpdateLeadHandler;
use App\Application\CampanasLeads\Queries\GetCampanaLeadByIdQuery;
use App\Application\CampanasLeads\Queries\GetLeadsQuery;
use App\Application\CampanasLeads\QueryHandlers\GetCampanaLeadByIdQueryHandler;
use App\Application\CampanasLeads\QueryHandlers\GetLeadsQueryHandler;
use App\Application\CampanasLeads\Services\ImportarLeadsExcelService;
use App\Http\Controllers\Controller;
use App\Http\Requests\CampanasLeads\StoreLeadRequest;
use App\Http\Requests\CampanasLeads\UpdateLeadRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function __construct(
        private readonly GetCampanaLeadByIdQueryHandler $getCampanaByIdHandler,
        private readonly GetLeadsQueryHandler $getLeadsHandler,
        private readonly CreateLeadHandler $createHandler,
        private readonly UpdateLeadHandler $updateHandler,
        private readonly DeleteLeadHandler $deleteHandler,
        private readonly ImportarLeadsExcelService $importarExcelService,
    ) {}

    public function index(Request $request, int $campanaLeadId): JsonResponse
    {
        $this->getCampanaByIdHandler->handle(new GetCampanaLeadByIdQuery($campanaLeadId));

        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 20),
            'query'     => $request->get('query', ''),
            'sortKey'   => $request->input('sort.key', 'created_at'),
            'sortOrder' => $request->input('sort.order', 'desc'),
        ]);

        return response()->json(
            $this->getLeadsHandler->handle(new GetLeadsQuery($campanaLeadId, $pagination))
        );
    }

    public function show(int $campanaLeadId, int $leadId): JsonResponse
    {
        return response()->json($this->getLeadsHandler->findById($campanaLeadId, $leadId));
    }

    public function store(StoreLeadRequest $request, int $campanaLeadId): JsonResponse
    {
        $this->getCampanaByIdHandler->handle(new GetCampanaLeadByIdQuery($campanaLeadId));

        $dto = $this->createHandler->handle(new CreateLeadCommand(
            campanaLeadId: $campanaLeadId,
            nombre:        $request->nombre,
            celular:       $request->celular,
            correo:        $request->correo,
            profesion:     $request->profesion,
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateLeadRequest $request, int $campanaLeadId, int $id): JsonResponse
    {
        return response()->json($this->updateHandler->handle(new UpdateLeadCommand(
            campanaLeadId: $campanaLeadId,
            id:            $id,
            nombre:        $request->nombre,
            celular:       $request->celular,
            correo:        $request->correo,
            profesion:     $request->profesion,
        )));
    }

    public function destroy(int $campanaLeadId, int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteLeadCommand($campanaLeadId, $id));

        return response()->json(null, 204);
    }

    public function importarExcel(Request $request, int $campanaLeadId): JsonResponse
    {
        $this->getCampanaByIdHandler->handle(new GetCampanaLeadByIdQuery($campanaLeadId));

        $request->validate([
            'archivo' => ['required', 'file', 'max:5120',
                'mimes:xlsx,xls,csv,text/csv,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        ]);

        $archivo   = $request->file('archivo');
        $extension = strtolower($archivo->getClientOriginalExtension());

        try {
            $resultado = $this->importarExcelService->importar($campanaLeadId, $archivo->getRealPath(), $extension);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'No se pudo leer el archivo: '.$e->getMessage()], 422);
        }

        return response()->json($resultado);
    }
}
