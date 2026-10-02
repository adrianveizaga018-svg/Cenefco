<?php

namespace App\Http\Controllers\Api;

use App\Application\Gastos\Commands\CreateGastoCommand;
use App\Application\Gastos\Commands\DeleteGastoCommand;
use App\Application\Gastos\Commands\UpdateGastoCommand;
use App\Application\Gastos\Handlers\CreateGastoHandler;
use App\Application\Gastos\Handlers\DeleteGastoHandler;
use App\Application\Gastos\Handlers\UpdateGastoHandler;
use App\Application\Gastos\Queries\GetGastoByIdQuery;
use App\Application\Gastos\Queries\GetGastosQuery;
use App\Application\Gastos\Queries\GetResumenMesQuery;
use App\Application\Gastos\QueryHandlers\GetGastoByIdQueryHandler;
use App\Application\Gastos\QueryHandlers\GetGastosQueryHandler;
use App\Application\Gastos\QueryHandlers\GetResumenMesQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Gastos\StoreGastoRequest;
use App\Http\Requests\Gastos\UpdateGastoRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Gastos')]
class GastoController extends Controller
{
    public function __construct(
        private readonly GetGastosQueryHandler     $getListHandler,
        private readonly GetGastoByIdQueryHandler  $getByIdHandler,
        private readonly GetResumenMesQueryHandler $getResumenHandler,
        private readonly CreateGastoHandler        $createHandler,
        private readonly UpdateGastoHandler        $updateHandler,
        private readonly DeleteGastoHandler        $deleteHandler,
    ) {}

    #[OA\Get(path: '/api/v1/gastos', summary: 'Listar gastos', tags: ['Gastos'])]
    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 15),
            'query'     => $request->get('query', ''),
            'sortKey'   => $request->get('sortKey', 'fecha'),
            'sortOrder' => $request->get('sortOrder', 'desc'),
        ], 'fecha');

        $filtros = $request->only(['categoria_gasto_id', 'campana_publicidad_id', 'fecha_desde', 'fecha_hasta']);

        return response()->json($this->getListHandler->handle(new GetGastosQuery($pagination, $filtros)));
    }

    #[OA\Get(path: '/api/v1/gastos/resumen-mes', summary: 'Resumen de gastos del mes', tags: ['Gastos'])]
    public function resumenMes(Request $request): JsonResponse
    {
        $anio = (int) $request->get('anio', now()->year);
        $mes  = (int) $request->get('mes', now()->month);

        return response()->json($this->getResumenHandler->handle(new GetResumenMesQuery($anio, $mes)));
    }

    #[OA\Get(path: '/api/v1/gastos/{id}', summary: 'Obtener gasto por ID', tags: ['Gastos'])]
    public function show(int $gasto): JsonResponse
    {
        return response()->json($this->getByIdHandler->handle(new GetGastoByIdQuery($gasto)));
    }

    #[OA\Post(path: '/api/v1/gastos', summary: 'Registrar gasto', tags: ['Gastos'])]
    public function store(StoreGastoRequest $request): JsonResponse
    {
        $comprobanteUrl = null;
        if ($request->hasFile('comprobante')) {
            $comprobanteUrl = $request->file('comprobante')->store('gastos-comprobantes', 'public');
        }

        $dto = $this->createHandler->handle(new CreateGastoCommand(
            categoria_gasto_id: $request->categoria_gasto_id,
            concepto:           $request->concepto,
            monto:              (float) $request->monto,
            fecha:              $request->fecha,
            responsable:        $request->responsable,
            comprobante_url:    $comprobanteUrl,
            nota:               $request->nota,
            campana_publicidad_id: $request->filled('campana_publicidad_id') ? (int) $request->campana_publicidad_id : null,
        ));

        return response()->json($dto, 201);
    }

    #[OA\Put(path: '/api/v1/gastos/{id}', summary: 'Actualizar gasto', tags: ['Gastos'])]
    public function update(UpdateGastoRequest $request, int $gasto): JsonResponse
    {
        $comprobanteUrl = null;
        if ($request->hasFile('comprobante')) {
            $comprobanteUrl = $request->file('comprobante')->store('gastos-comprobantes', 'public');
        }

        return response()->json($this->updateHandler->handle(new UpdateGastoCommand(
            id:                 $gasto,
            categoria_gasto_id: $request->categoria_gasto_id,
            concepto:           $request->concepto,
            monto:              $request->has('monto') ? (float) $request->monto : null,
            fecha:              $request->fecha,
            responsable:        $request->responsable,
            comprobante_url:    $comprobanteUrl,
            nota:               $request->nota,
            campana_publicidad_id: $request->filled('campana_publicidad_id') ? (int) $request->campana_publicidad_id : null,
        )));
    }

    #[OA\Delete(path: '/api/v1/gastos/{id}', summary: 'Eliminar gasto', tags: ['Gastos'])]
    public function destroy(int $gasto): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteGastoCommand($gasto));

        return response()->json(null, 204);
    }
}
