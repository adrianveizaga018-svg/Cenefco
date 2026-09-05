<?php

namespace App\Http\Controllers\Api;

use App\Application\Pagos\Commands\AnularPagoCommand;
use App\Application\Pagos\Commands\CreatePagoCommand;
use App\Application\Pagos\Commands\ObservarPagoCommand;
use App\Application\Pagos\Commands\UpdatePagoCommand;
use App\Application\Pagos\Commands\VerificarPagoCommand;
use App\Application\Pagos\Handlers\AnularPagoHandler;
use App\Application\Pagos\Handlers\CreatePagoHandler;
use App\Application\Pagos\Handlers\ObservarPagoHandler;
use App\Application\Pagos\Handlers\UpdatePagoHandler;
use App\Application\Pagos\Handlers\VerificarPagoHandler;
use App\Application\Pagos\Queries\GetPagoByIdQuery;
use App\Application\Pagos\Queries\GetPagosQuery;
use App\Application\Pagos\QueryHandlers\GetPagoByIdQueryHandler;
use App\Application\Pagos\QueryHandlers\GetPagosQueryHandler;
use App\Domain\Pagos\Contracts\PagoRepositoryInterface;
use App\Domain\Pagos\Exceptions\PagoNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pagos\StorePagoRequest;
use App\Http\Requests\Pagos\UpdatePagoRequest;
use App\Infrastructure\Vendedores\Services\VendedorScopeResolver;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PagoController extends Controller
{
    public function __construct(
        private readonly GetPagosQueryHandler    $getPagosHandler,
        private readonly GetPagoByIdQueryHandler $getPagoByIdHandler,
        private readonly CreatePagoHandler       $createHandler,
        private readonly UpdatePagoHandler       $updateHandler,
        private readonly AnularPagoHandler       $anularHandler,
        private readonly VerificarPagoHandler    $verificarHandler,
        private readonly ObservarPagoHandler     $observarHandler,
        private readonly PagoRepositoryInterface $repository,
        private readonly VendedorScopeResolver   $vendedorScope,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 30),
        ]);

        $filters = [
            'id_us'           => $request->get('id_us'),
            'id_fechapago'    => $request->get('id_fechapago'),
            'conInactivos'    => $request->boolean('conInactivos', false),
            'idImpPermitidos' => $this->vendedorScope->idImpPermitidos(auth()->user()),
        ];

        return response()->json(
            $this->getPagosHandler->handle(new GetPagosQuery($pagination, $filters))
        );
    }

    public function show(int $id): JsonResponse
    {
        $pago = $this->repository->findById($id);
        if (! $pago) {
            throw new PagoNotFoundException($id);
        }
        $this->vendedorScope->assertAccesoInscripcion(auth()->user(), $pago->id_ins ?? null);

        return response()->json(
            $this->getPagoByIdHandler->handle(new GetPagoByIdQuery($id))
        );
    }

    public function store(StorePagoRequest $request): JsonResponse
    {
        $this->vendedorScope->assertAccesoInscripcion(auth()->user(), $request->filled('id_ins') ? (int) $request->id_ins : null);

        $comprobantePath = null;
        if ($request->hasFile('comprobante_archivo')) {
            $comprobantePath = $request->file('comprobante_archivo')->store('comprobantes-pagos', 'public');
        }

        $dto = $this->createHandler->handle(new CreatePagoCommand(
            idUs:          $request->id_us,
            montoPagado:   (float) $request->monto_pagado,
            idFechapago:   $request->id_fechapago,
            idMat:         $request->id_mat,
            idIns:         $request->id_ins,
            nroBoleta:     $request->nro_boleta_bancaria,
            fechaDeposito: $request->fecha_deposito,
            nroNit:        $request->nro_nit,
            nombreNit:     $request->nombre_nit,
            tipoFechapago: $request->tipo_fechapago,
            observacion:   $request->observacion_pago,
            idUsReg:       auth()->id() ?? 0,
            metodoPago:    $request->metodo_pago,
            idUsCajero:    auth()->id(),
            comprobanteArchivo: $comprobantePath,
            montoDescuento: $request->filled('monto_descuento') ? (float) $request->monto_descuento : null,
            motivoDescuento: $request->motivo_descuento,
            tipoBancoId:    $request->filled('tipo_banco_id') ? (int) $request->tipo_banco_id : null,
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdatePagoRequest $request, int $id): JsonResponse
    {
        $pago = $this->repository->findById($id);
        if (! $pago) {
            throw new PagoNotFoundException($id);
        }

        $esAdmin = auth()->user()->esAdmin();
        if ($pago->estado_verificacion === 'verificado') {
            return response()->json(['error' => 'El pago ya fue verificado y no puede editarse.'], 403);
        }
        if (! $esAdmin && (int) $pago->id_us_cajero !== (int) auth()->id()) {
            return response()->json(['error' => 'No autorizado para editar este pago.'], 403);
        }
        $this->vendedorScope->assertAccesoInscripcion(auth()->user(), $pago->id_ins ?? null);

        $data = $request->validated();
        if (array_key_exists('monto_descuento', $data)) {
            $data['monto_descuento_extra'] = $data['monto_descuento'];
            unset($data['monto_descuento']);
        }

        if ($request->hasFile('comprobante_archivo')) {
            $data['comprobante_archivo'] = $request->file('comprobante_archivo')->store('comprobantes-pagos', 'public');
        } else {
            unset($data['comprobante_archivo']);
        }

        return response()->json(
            $this->updateHandler->handle(new UpdatePagoCommand(
                id:       $id,
                idUsReg:  auth()->id() ?? 0,
                data:     $data,
            ))
        );
    }

    public function verificar(int $id): JsonResponse
    {
        if (! auth()->user()->esAdmin()) {
            return response()->json(['error' => 'No autorizado.'], 403);
        }

        return response()->json(
            $this->verificarHandler->handle(new VerificarPagoCommand(
                id:      $id,
                idUsReg: auth()->id() ?? 0,
            ))
        );
    }

    public function observar(Request $request, int $id): JsonResponse
    {
        if (! auth()->user()->esAdmin()) {
            return response()->json(['error' => 'No autorizado.'], 403);
        }

        $request->validate([
            'nota_verificacion' => ['required', 'string', 'min:3', 'max:1000'],
        ]);

        return response()->json(
            $this->observarHandler->handle(new ObservarPagoCommand(
                id:      $id,
                nota:    $request->string('nota_verificacion')->toString(),
                idUsReg: auth()->id() ?? 0,
            ))
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $pago = $this->repository->findById($id);
        if (! $pago) {
            throw new PagoNotFoundException($id);
        }
        $this->vendedorScope->assertAccesoInscripcion(auth()->user(), $pago->id_ins ?? null);

        $this->anularHandler->handle(new AnularPagoCommand(
            id:      $id,
            idUsReg: auth()->id() ?? 0,
        ));

        return response()->json(null, 204);
    }
}
