<?php

namespace App\Http\Controllers\Api\Reportes;

use App\Application\Cursos\Support\RangoPeriodoResolver;
use App\Application\Reportes\Queries\GetCuotasCursoQuery;
use App\Application\Reportes\Queries\GetVentasPorPeriodoQuery;
use App\Application\Reportes\QueryHandlers\GetCuotasCursoQueryHandler;
use App\Application\Reportes\QueryHandlers\GetVentasPorPeriodoQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Reportes\CuotasCursoRequest;
use App\Http\Requests\Reportes\VentasPorPeriodoRequest;
use App\Infrastructure\Vendedores\Services\VendedorScopeResolver;
use Illuminate\Http\JsonResponse;

class ReporteController extends Controller
{
    public function __construct(
        private readonly GetVentasPorPeriodoQueryHandler $ventasPorPeriodoHandler,
        private readonly GetCuotasCursoQueryHandler $cuotasCursoHandler,
        private readonly VendedorScopeResolver $vendedorScope,
    ) {}

    public function ventasPorPeriodo(VentasPorPeriodoRequest $request): JsonResponse
    {
        $resultado = $this->ventasPorPeriodoHandler->handle(
            new GetVentasPorPeriodoQuery(
                fechaInicio: $request->fecha_inicio,
                fechaFin:    $request->fecha_fin,
                usuarioId:   $request->usuario_id,
            )
        );

        return response()->json($resultado);
    }

    public function cuotasCurso(CuotasCursoRequest $request): JsonResponse
    {
        $idImp = $request->filled('id_imp') ? (int) $request->id_imp : null;

        $this->vendedorScope->assertAccesoImparte(auth()->user(), $idImp);

        if ($request->filled('periodo')) {
            [$inicio, $fin] = RangoPeriodoResolver::resolver(
                $request->periodo,
                $request->fecha ?? now()->toDateString(),
                $request->fecha_fin,
            );
            $fechaInicio = $inicio->toDateString();
            $fechaFin    = $fin->toDateString();
        } else {
            $fechaInicio = $request->fecha_inicio;
            $fechaFin    = $request->fecha_fin;
        }

        $resultado = $this->cuotasCursoHandler->handle(
            new GetCuotasCursoQuery(
                idImp:           $idImp,
                fechaInicio:     $fechaInicio,
                fechaFin:        $fechaFin,
                conInactivos:    $request->boolean('con_inactivos', false),
                idImpPermitidos: $this->vendedorScope->idImpPermitidos(auth()->user()),
            )
        );

        return response()->json($resultado);
    }
}
