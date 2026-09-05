<?php

namespace App\Http\Controllers\Api;

use App\Application\Comisiones\Commands\AnularComisionCommand;
use App\Application\Comisiones\Commands\AprobarComisionCommand;
use App\Application\Comisiones\Commands\CreateComisionLiquidacionCommand;
use App\Application\Comisiones\Commands\PagarComisionCommand;
use App\Application\Comisiones\Handlers\AnularComisionHandler;
use App\Application\Comisiones\Handlers\AprobarComisionHandler;
use App\Application\Comisiones\Handlers\CreateComisionLiquidacionHandler;
use App\Application\Comisiones\Handlers\PagarComisionHandler;
use App\Application\Comisiones\Queries\GetComisionByIdQuery;
use App\Application\Comisiones\Queries\GetComisionesQuery;
use App\Application\Comisiones\Queries\GetComisionSugeridaQuery;
use App\Application\Comisiones\QueryHandlers\GetComisionByIdQueryHandler;
use App\Application\Comisiones\QueryHandlers\GetComisionesQueryHandler;
use App\Application\Comisiones\QueryHandlers\GetComisionSugeridaQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Comisiones\PagarComisionRequest;
use App\Http\Requests\Comisiones\StoreComisionLiquidacionRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ComisionController extends Controller
{
    public function __construct(
        private readonly GetComisionesQueryHandler        $getListHandler,
        private readonly GetComisionByIdQueryHandler       $getByIdHandler,
        private readonly GetComisionSugeridaQueryHandler   $getSugeridaHandler,
        private readonly CreateComisionLiquidacionHandler  $createHandler,
        private readonly AprobarComisionHandler            $aprobarHandler,
        private readonly PagarComisionHandler              $pagarHandler,
        private readonly AnularComisionHandler             $anularHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 20),
        ]);

        return response()->json(
            $this->getListHandler->handle(new GetComisionesQuery(
                pagination:  $pagination,
                vendedorId:  $request->filled('vendedor_id') ? (int) $request->get('vendedor_id') : null,
                estado:      $request->filled('estado') ? $request->get('estado') : null,
            ))
        );
    }

    public function sugerida(Request $request): JsonResponse
    {
        $request->validate([
            'vendedor_id' => ['required', 'integer', 'exists:vendedores,id'],
            'fecha_desde' => ['required', 'date'],
            'fecha_hasta' => ['required', 'date', 'after_or_equal:fecha_desde'],
        ]);

        return response()->json(
            $this->getSugeridaHandler->handle(new GetComisionSugeridaQuery(
                vendedorId:  (int) $request->vendedor_id,
                fechaDesde:  $request->fecha_desde,
                fechaHasta:  $request->fecha_hasta,
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->getByIdHandler->handle(new GetComisionByIdQuery($id))
        );
    }

    public function store(StoreComisionLiquidacionRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateComisionLiquidacionCommand(
            vendedorId:  (int) $request->vendedor_id,
            fechaDesde:  $request->fecha_desde,
            fechaHasta:  $request->fecha_hasta,
            nota:        $request->nota,
        ));

        return response()->json($dto, 201);
    }

    public function aprobar(int $id): JsonResponse
    {
        $dto = $this->aprobarHandler->handle(new AprobarComisionCommand(
            id:          $id,
            aprobadoPor: auth()->id() ?? 0,
        ));

        return response()->json($dto);
    }

    public function pagar(PagarComisionRequest $request, int $id): JsonResponse
    {
        $comprobantePath = $request->file('comprobante_pago')->store('comprobantes-comisiones', 'public');

        $dto = $this->pagarHandler->handle(new PagarComisionCommand(
            id:                   $id,
            pagadoPor:            auth()->id() ?? 0,
            comprobantePagoUrl:   $comprobantePath,
        ));

        return response()->json($dto);
    }

    public function anular(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'nota' => ['nullable', 'string', 'max:1000'],
        ]);

        $dto = $this->anularHandler->handle(new AnularComisionCommand(
            id:   $id,
            nota: $request->nota,
        ));

        return response()->json($dto);
    }
}
