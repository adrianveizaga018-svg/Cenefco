<?php

namespace App\Http\Controllers\Api;

use App\Application\CampanasPublicidad\Services\SeguimientoCampanaService;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** Registro diario de marketing, resumen del periodo e informes en PDF de una campaña. */
class SeguimientoCampanaController extends Controller
{
    public function __construct(private readonly SeguimientoCampanaService $seguimiento) {}

    public function show(int $id, Request $request): JsonResponse
    {
        try {
            [$desde, $hasta] = $this->periodo($request, $id);

            return response()->json([
                'registros' => $this->seguimiento->registros($id),
                'resumen'   => $this->seguimiento->resumen($id, $desde, $hasta),
                'informes'  => $this->seguimiento->informes($id),
            ]);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    public function guardar(int $id, Request $request): JsonResponse
    {
        $datos = $request->validate([
            'fecha'        => 'required|date|before_or_equal:today',
            'nuevos_grupo' => 'required|integer|min:0|max:100000',
            'total_grupo'  => 'nullable|integer|min:0|max:1000000',
            'gasto_extra'  => 'nullable|numeric|min:0|max:1000000',
            'nota'         => 'nullable|string|max:1000',
            'flyer'        => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        try {
            $this->seguimiento->guardarRegistro($id, $datos, $request->file('flyer'), (int) $request->user()->id);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }

        return response()->json(['message' => 'Registro del día guardado.'], 201);
    }

    public function eliminar(int $idRegistro): JsonResponse
    {
        try {
            $this->seguimiento->eliminarRegistro($idRegistro);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }

        return response()->json(['message' => 'Registro eliminado.']);
    }

    public function generarInforme(int $id, Request $request): JsonResponse
    {
        $request->validate(['desde' => 'required|date', 'hasta' => 'required|date|after_or_equal:desde']);

        try {
            $informe = $this->seguimiento->generarInforme($id, Carbon::parse($request->desde), Carbon::parse($request->hasta), $request->user());
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }

        return response()->json(['message' => 'Informe generado y guardado en el historial.', 'informe' => $informe], 201);
    }

    /** Por defecto: desde que empezó la campaña (o hace 30 días) hasta hoy. */
    private function periodo(Request $request, int $id): array
    {
        $campana = $this->seguimiento->campana($id);
        $desde = $request->filled('desde') ? Carbon::parse($request->desde)
            : ($campana->fecha_inicio ? Carbon::parse($campana->fecha_inicio) : now()->subDays(30));
        $hasta = $request->filled('hasta') ? Carbon::parse($request->hasta) : now();

        return [$desde, $hasta];
    }
}
