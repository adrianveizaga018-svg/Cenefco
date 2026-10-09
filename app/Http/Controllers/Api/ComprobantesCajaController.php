<?php

namespace App\Http\Controllers\Api;

use App\Application\Inscripciones\Services\ComprobanteEstudianteService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** Bandeja de Caja para los comprobantes que suben los estudiantes desde su panel. */
class ComprobantesCajaController extends Controller
{
    public function __construct(private readonly ComprobanteEstudianteService $comprobantes) {}

    public function index(Request $request): JsonResponse
    {
        $estado = $request->query('estado', 'por_revisar');

        return response()->json($this->comprobantes->listar(is_string($estado) ? $estado : 'por_revisar'));
    }

    public function pendientes(): JsonResponse
    {
        return response()->json(['pendientes' => $this->comprobantes->pendientes()]);
    }

    public function aprobar(Request $request, int $idPago): JsonResponse
    {
        try {
            $res = $this->comprobantes->aprobar($idPago, $request->user());
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Pago aprobado. Le avisamos al estudiante por correo.', ...$res]);
    }

    public function rechazar(Request $request, int $idPago): JsonResponse
    {
        $datos = $request->validate(['motivo' => 'required|string|min:5|max:500']);

        try {
            $this->comprobantes->rechazar($idPago, $request->user(), trim($datos['motivo']));
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Comprobante rechazado. Le avisamos al estudiante con el motivo.']);
    }
}
