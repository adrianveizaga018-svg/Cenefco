<?php

namespace App\Http\Controllers\Api;

use App\Application\Certificados\Services\CertificadoInscripcionService;
use App\Application\Inscripciones\Services\FichaEstudianteService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FichaEstudianteController extends Controller
{
    public function __construct(
        private readonly FichaEstudianteService $fichas,
        private readonly CertificadoInscripcionService $certificados,
    ) {}

    /** GET /estudiantes/buscar?q= — carnet o nombre */
    public function buscar(Request $request): JsonResponse
    {
        return response()->json($this->fichas->buscar((string) $request->query('q', '')));
    }

    /** GET /estudiantes/{idUs}/ficha */
    public function ficha(int $idUs): JsonResponse
    {
        $ficha = $this->fichas->ficha($idUs);

        return $ficha
            ? response()->json($ficha)
            : response()->json(['message' => 'No se encontró al estudiante.'], 404);
    }

    /** GET /inscripciones/{idIns}/certificado-opciones */
    public function opcionesCertificado(int $idIns): JsonResponse
    {
        try {
            return response()->json($this->certificados->opciones($idIns));
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    /** POST /inscripciones/{idIns}/emitir-certificado  { plantilla_id } */
    public function emitirCertificado(Request $request, int $idIns): JsonResponse
    {
        $data = $request->validate(['plantilla_id' => ['required', 'integer']]);

        try {
            $resultado = $this->certificados->emitir($idIns, (int) $data['plantilla_id']);
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\RuntimeException $e) {
            report($e);

            return response()->json(['message' => $e->getMessage()], 422);
        }

        if (($resultado['generados'] ?? 0) < 1) {
            $detalle = $resultado['errores'][0]['error'] ?? 'no se generó ningún certificado';

            return response()->json(['message' => 'No se pudo emitir el certificado: '.$detalle, 'resultado' => $resultado], 422);
        }

        return response()->json([
            'message'     => 'Certificado emitido.',
            'certificado' => $resultado['certificados'][0] ?? null,
        ], 201);
    }
}
