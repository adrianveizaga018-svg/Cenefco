<?php

namespace App\Http\Controllers\Api;

use App\Application\Ventas\Commands\EnviarComprobanteCorreoCommand;
use App\Application\Ventas\Handlers\EnviarComprobanteCorreoHandler;
use App\Application\Ventas\Queries\GetReporteVentasQuery;
use App\Application\Ventas\Queries\GetVentaByIdQuery;
use App\Application\Ventas\Queries\GetVentasQuery;
use App\Application\Ventas\QueryHandlers\GenerarComprobantePdfHandler;
use App\Application\Ventas\QueryHandlers\GetReporteVentasQueryHandler;
use App\Application\Ventas\QueryHandlers\GetVentaByIdQueryHandler;
use App\Application\Ventas\QueryHandlers\GetVentasQueryHandler;
use App\Domain\Ventas\Contracts\VentaRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VentaController extends Controller
{
    public function __construct(
        private readonly GetVentasQueryHandler          $getVentasHandler,
        private readonly GetVentaByIdQueryHandler       $getVentaByIdHandler,
        private readonly GetReporteVentasQueryHandler   $getReporteHandler,
        private readonly GenerarComprobantePdfHandler   $pdfHandler,
        private readonly EnviarComprobanteCorreoHandler $enviarCorreoHandler,
        private readonly VentaRepositoryInterface       $ventaRepository,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 30),
            'query'     => $request->get('query', ''),
        ]);

        return response()->json(
            $this->getVentasHandler->handle(new GetVentasQuery(
                pagination:   $pagination,
                estadoPago:   $request->filled('estado_pago') ? $request->get('estado_pago') : null,
                periodo:      $request->filled('periodo') ? $request->get('periodo') : null,
                gestion:      $request->filled('gestion') ? $request->get('gestion') : null,
                conInactivos: $request->boolean('conInactivos', false),
                canalVenta:   $request->filled('canal_venta') ? $request->get('canal_venta') : null,
                idVendedor:   $request->filled('id_vendedor') ? $request->integer('id_vendedor') : null,
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->getVentaByIdHandler->handle(new GetVentaByIdQuery($id))
        );
    }

    public function pdf(int $id): Response
    {
        return $this->pdfHandler->handle($id);
    }

    public function enviarCorreo(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'email' => ['nullable', 'email'],
        ]);

        $destinatario = $this->enviarCorreoHandler->handle(new EnviarComprobanteCorreoCommand(
            idIns: $id,
            email: $request->filled('email') ? $request->string('email')->toString() : null,
        ));

        return response()->json(['message' => 'Comprobante enviado correctamente.', 'email' => $destinatario]);
    }

    public function reporte(Request $request): JsonResponse
    {
        return response()->json(
            $this->getReporteHandler->handle(new GetReporteVentasQuery(
                query:   $request->filled('query') ? $request->get('query') : null,
                periodo: $request->filled('periodo') ? $request->get('periodo') : null,
                gestion: $request->filled('gestion') ? $request->get('gestion') : null,
            ))
        );
    }

    public function reportePorVendedor(Request $request): JsonResponse
    {
        return response()->json(
            $this->ventaRepository->reportePorVendedor([
                'gestion' => $request->filled('gestion') ? $request->get('gestion') : null,
                'periodo' => $request->filled('periodo') ? $request->get('periodo') : null,
            ])
        );
    }

    public function reportePorCanal(Request $request): JsonResponse
    {
        return response()->json(
            $this->ventaRepository->reportePorCanal([
                'gestion' => $request->filled('gestion') ? $request->get('gestion') : null,
                'periodo' => $request->filled('periodo') ? $request->get('periodo') : null,
            ])
        );
    }

    public function proyeccionCobros(Request $request): JsonResponse
    {
        $meses = $request->integer('meses', 6);
        return response()->json(
            $this->ventaRepository->proyeccionCobros(max(1, min($meses, 24)))
        );
    }
}
