<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Infrastructure\CertificadosExternos\CertificadosExternosService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CertificadoExternoController extends Controller
{
    public function __construct(
        private readonly CertificadosExternosService $service,
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $response = $this->service->listarCursos($request->get('q'));
        } catch (ConnectionException $e) {
            return response()->json(['message' => 'No se pudo conectar con el servicio de certificados.'], 502);
        }

        return response()->json($response->json(), $response->status());
    }

    public function registrarEstudiante(Request $request, int $cursoId): JsonResponse
    {
        $data = $request->validate([
            'nombre'    => ['required_without:nombres', 'nullable', 'string', 'max:300'],
            'nombres'   => ['required_without:nombre', 'nullable', 'array', 'min:1'],
            'nombres.*' => ['string', 'max:300'],
        ]);

        try {
            $response = isset($data['nombres'])
                ? $this->service->registrarEstudiantes($cursoId, $data['nombres'])
                : $this->service->registrarEstudiante($cursoId, $data['nombre']);
        } catch (ConnectionException $e) {
            return response()->json(['message' => 'No se pudo conectar con el servicio de certificados.'], 502);
        }

        return response()->json($response->json(), $response->status());
    }
}
