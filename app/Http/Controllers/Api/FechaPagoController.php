<?php

namespace App\Http\Controllers\Api;

use App\Application\Pagos\Commands\CreateFechaPagoCommand;
use App\Application\Pagos\Commands\GenerarLoteFechasPagoCommand;
use App\Application\Pagos\Commands\UpdateFechaPagoCommand;
use App\Application\Pagos\Handlers\CreateFechaPagoHandler;
use App\Application\Pagos\Handlers\GenerarLoteFechasPagoHandler;
use App\Application\Pagos\Handlers\UpdateFechaPagoHandler;
use App\Application\Pagos\Queries\GetFechasPagoQuery;
use App\Application\Pagos\QueryHandlers\GetFechasPagoQueryHandler;
use App\Domain\Pagos\Contracts\FechaPagoRepositoryInterface;
use App\Domain\Pagos\Exceptions\FechaPagoNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pagos\GenerarLoteFechasPagoRequest;
use App\Http\Requests\Pagos\StoreFechaPagoRequest;
use App\Http\Requests\Pagos\UpdateFechaPagoRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FechaPagoController extends Controller
{
    public function __construct(
        private readonly GetFechasPagoQueryHandler    $getFechasPagoHandler,
        private readonly CreateFechaPagoHandler       $createHandler,
        private readonly UpdateFechaPagoHandler       $updateHandler,
        private readonly GenerarLoteFechasPagoHandler $generarLoteHandler,
        private readonly FechaPagoRepositoryInterface $repository,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 30),
        ]);

        $filters = [
            'id_plan'      => $request->get('id_plan'),
            'tipo_tramite' => $request->get('tipo_tramite'),
            'conInactivos' => $request->boolean('conInactivos', false),
        ];

        return response()->json(
            $this->getFechasPagoHandler->handle(new GetFechasPagoQuery($pagination, $filters))
        );
    }

    public function show(int $id): JsonResponse
    {
        $model = $this->repository->findById($id);
        if (! $model) {
            throw new FechaPagoNotFoundException($id);
        }

        return response()->json($model);
    }

    public function store(StoreFechaPagoRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateFechaPagoCommand(
            idPlan:      $request->id_plan,
            nroPago:     $request->nro_pago,
            montoAPagar: (float) $request->monto_a_pagar,
            fechaInicio: $request->fecha_inicio,
            fechaFin:    $request->fecha_fin,
            obligatorio: $request->boolean('obligatorio', true) ? 1 : 0,
            tipoTramite: $request->tipo_tramite,
            idUsReg:     auth()->id() ?? 0,
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateFechaPagoRequest $request, int $id): JsonResponse
    {
        return response()->json(
            $this->updateHandler->handle(new UpdateFechaPagoCommand(
                id:   $id,
                data: $request->validated(),
            ))
        );
    }

    public function generarLote(GenerarLoteFechasPagoRequest $request): JsonResponse
    {
        $fechas = $this->generarLoteHandler->handle(new GenerarLoteFechasPagoCommand(
            idPlan:      $request->id_plan,
            nroCuotas:   (int) $request->nro_cuotas,
            montoTotal:  (float) $request->monto_total,
            fechaInicio: $request->fecha_inicio,
            intervalo:   $request->intervalo,
            tipoTramite: $request->tipo_tramite,
            idUsReg:     auth()->id() ?? 0,
        ));

        return response()->json(['data' => $fechas, 'total' => count($fechas)], 201);
    }

    public function destroy(int $id): JsonResponse
    {
        $model = $this->repository->findById($id);
        if (! $model) {
            throw new FechaPagoNotFoundException($id);
        }

        $this->repository->anular($id);

        return response()->json(null, 204);
    }
}
