<?php

namespace App\Http\Controllers\Api;

use App\Application\Comisiones\Services\ComisionCalculadorService;
use App\Application\Comisiones\Services\RankingVentasService;
use App\Application\Comisiones\Services\TablaComisionesService;
use App\Application\Comisiones\DTOs\ComisionLiquidacionDetalleDTO;
use App\Http\Controllers\Controller;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** Tabla de comisiones (Administración/Contabilidad), ventas con su comisión y ranking de vendedores. */
class ComisionTablaController extends Controller
{
    public function __construct(
        private readonly TablaComisionesService $tabla,
        private readonly ComisionCalculadorService $calculador,
        private readonly RankingVentasService $ranking,
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->tabla->tabla());
    }

    public function guardarProducto(Request $request, ?int $id = null): JsonResponse
    {
        $datos = $request->validate([
            'nombre'                 => 'required|string|max:120',
            'modalidad'              => 'required|in:rangos,negociar',
            'porcentaje_minimo_pago' => 'required|integer|min:1|max:100',
            'activo'                 => 'boolean',
            'rangos'                 => 'array|max:30',
            'rangos.*.desde'         => 'required|numeric|min:0',
            'rangos.*.hasta'         => 'nullable|numeric|min:0',
            'rangos.*.monto'         => 'required|numeric|min:0',
        ]);

        return $this->responder(fn () => ['id' => $this->tabla->guardarProducto($id, $datos)], $id ? 200 : 201);
    }

    public function asignarProgramas(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'asignaciones'               => 'required|array|max:500',
            'asignaciones.*.id_programa' => 'required|integer',
            'asignaciones.*.producto_id' => 'nullable|integer|exists:comision_productos,id',
        ]);

        return response()->json(['actualizados' => $this->tabla->asignarProgramas($datos['asignaciones'])]);
    }

    /** Todas las ventas del periodo con su comisión (también las pendientes de acordar y sin vendedor). */
    public function ventas(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'desde'       => 'required|date',
            'hasta'       => 'required|date|after_or_equal:desde',
            'vendedor_id' => 'nullable|integer',
        ]);

        $ventas = $this->calculador->ventas($datos['desde'], $datos['hasta'], isset($datos['vendedor_id']) ? (int) $datos['vendedor_id'] : null);

        return response()->json(['data' => $ventas->map(fn ($v) => ComisionLiquidacionDetalleDTO::fromModel($v))->values()]);
    }

    public function acordar(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'id_ins'      => 'required|integer',
            'monto'       => 'nullable|numeric|min:0',
            'vendedor_id' => 'nullable|integer|exists:vendedores,id',
            'motivo'      => 'required|string|min:3|max:500',
        ]);

        return $this->responder(function () use ($datos, $request) {
            $this->tabla->acordar((int) $datos['id_ins'], isset($datos['monto']) ? (float) $datos['monto'] : null,
                isset($datos['vendedor_id']) ? (int) $datos['vendedor_id'] : null, $datos['motivo'], (int) $request->user()->id);

            return ['message' => 'Comisión acordada.'];
        });
    }

    public function quitarAcuerdo(int $idIns): JsonResponse
    {
        return $this->responder(function () use ($idIns) {
            $this->tabla->quitarAcuerdo($idIns);

            return ['message' => 'Se volvió a la comisión de la tabla.'];
        });
    }

    /** Ranking del mes: lo ve el equipo de ventas y quien maneja comisiones. */
    public function ranking(Request $request): JsonResponse
    {
        $usuario = $request->user();
        if (! $usuario->tienePermiso('crm.ver') && ! $usuario->tienePermiso('comisiones.ver')) {
            return response()->json(['error' => 'Acceso denegado.'], 403);
        }
        $periodo = $request->validate(['mes' => 'nullable|date_format:Y-m'])['mes'] ?? now()->format('Y-m');

        return response()->json($this->ranking->ranking($periodo, (int) $usuario->id) + ['historial' => $this->ranking->historial()]);
    }

    private function responder(callable $accion, int $ok = 200): JsonResponse
    {
        try {
            return response()->json($accion(), $ok);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
