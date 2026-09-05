<?php

namespace App\Http\Controllers\Api;

use App\Application\Gastos\Commands\ConfirmarGastoRecurrenteCommand;
use App\Application\Gastos\Commands\CreateGastoRecurrenteCommand;
use App\Application\Gastos\Handlers\ConfirmarGastoRecurrenteHandler;
use App\Application\Gastos\Handlers\CreateGastoRecurrenteHandler;
use App\Application\Gastos\Queries\GetGastosRecurrentesQuery;
use App\Application\Gastos\QueryHandlers\GetGastosRecurrentesQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Gastos\ConfirmarGastoRecurrenteRequest;
use App\Http\Requests\Gastos\StoreGastoRecurrenteRequest;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Gastos')]
class GastoRecurrenteController extends Controller
{
    public function __construct(
        private readonly GetGastosRecurrentesQueryHandler $getListHandler,
        private readonly CreateGastoRecurrenteHandler      $createHandler,
        private readonly ConfirmarGastoRecurrenteHandler   $confirmarHandler,
    ) {}

    #[OA\Get(path: '/api/v1/gastos-recurrentes', summary: 'Listar gastos recurrentes activos', tags: ['Gastos'])]
    public function index(): JsonResponse
    {
        return response()->json($this->getListHandler->handle(new GetGastosRecurrentesQuery()));
    }

    #[OA\Post(path: '/api/v1/gastos-recurrentes', summary: 'Crear gasto recurrente', tags: ['Gastos'])]
    public function store(StoreGastoRecurrenteRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateGastoRecurrenteCommand(
            categoria_gasto_id: $request->categoria_gasto_id,
            concepto:           $request->concepto,
            monto:              (float) $request->monto,
            dia_del_mes:        (int) $request->dia_del_mes,
            activo:             $request->boolean('activo', true),
        ));

        return response()->json($dto, 201);
    }

    #[OA\Patch(path: '/api/v1/gastos-recurrentes/{id}/confirmar', summary: 'Confirmar gasto recurrente del mes', tags: ['Gastos'])]
    public function confirmar(ConfirmarGastoRecurrenteRequest $request, int $gastoRecurrente): JsonResponse
    {
        $comprobanteUrl = null;
        if ($request->hasFile('comprobante')) {
            $comprobanteUrl = $request->file('comprobante')->store('gastos-comprobantes', 'public');
        }

        $dto = $this->confirmarHandler->handle(new ConfirmarGastoRecurrenteCommand(
            gasto_recurrente_id: $gastoRecurrente,
            fecha:               $request->fecha,
            comprobante_url:     $comprobanteUrl,
        ));

        return response()->json($dto, 201);
    }
}
