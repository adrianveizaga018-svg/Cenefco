<?php

namespace App\Http\Controllers\Api;

use App\Application\Inscripciones\Services\AreaEstudianteService;
use App\Application\Inscripciones\Services\ComprobanteEstudianteService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** Área del estudiante: todo se filtra por la cuenta autenticada. */
class EstudiantePortalController extends Controller
{
    public function __construct(private readonly AreaEstudianteService $area) {}

    public function dashboard(Request $request): JsonResponse
    {
        $user  = $request->user();
        $idUs  = $this->area->idUs($user);
        $pagos = collect($this->area->pagos($idUs));

        $proxima = $pagos
            ->flatMap(fn ($p) => collect($p['cuotas'])->where('pagado', false)->map(fn ($q) => $q + ['programa' => $p['nombre_programa']]))
            ->sortBy(fn ($q) => $q['fecha_limite'] ?? '9999-12-31')
            ->first();

        $cursos = $this->area->cursos($idUs);

        return response()->json([
            'usuario' => [
                'id'     => $user->id,
                'nombre' => trim(($user->nombre ?? '') . ' ' . ($user->apellido ?? '')),
                'email'  => $user->email,
                'ci'     => $user->ci ?? null,
            ],
            'resumen' => [
                'cursos_activos'  => $cursos->where('estado_curso', '!=', 'finalizado')->count(),
                'certificados'    => $this->area->certificados($idUs)->count(),
                'clases_grabadas' => $cursos->sum(fn ($c) => count($c->grabaciones)),
                'deuda_pendiente' => (float) $pagos->sum('saldo'),
            ],
            'proxima_cuota' => $proxima,
            'cursos'        => $cursos->take(4)->values(),
            'recomendados'  => array_slice($this->area->catalogo($idUs), 0, 3),
        ]);
    }

    public function misCursos(Request $request): JsonResponse
    {
        return response()->json(['data' => $this->area->cursos($this->area->idUs($request->user()))]);
    }

    public function misCertificados(Request $request): JsonResponse
    {
        return response()->json(['data' => $this->area->certificados($this->area->idUs($request->user()))]);
    }

    public function misPagos(Request $request): JsonResponse
    {
        return response()->json(['data' => $this->area->pagos($this->area->idUs($request->user()))]);
    }

    public function catalogo(Request $request): JsonResponse
    {
        return response()->json(['data' => $this->area->catalogo($this->area->idUs($request->user()))]);
    }

    public function inscribirGratis(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_imp'  => 'required|integer',
            'id_plan' => 'required|integer',
        ]);

        try {
            $res = $this->area->inscribirGratis($request->user(), (int) $data['id_imp'], (int) $data['id_plan']);
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => "Te inscribiste en {$res['programa']}.", ...$res], 201);
    }

    public function infoPago(Request $request, int $idIns, ComprobanteEstudianteService $comprobantes): JsonResponse
    {
        try {
            return response()->json($comprobantes->infoPago($request->user(), $idIns));
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    public function bancos(ComprobanteEstudianteService $comprobantes): JsonResponse
    {
        return response()->json($comprobantes->bancos());
    }

    /** Comprobante para una cuota de una inscripción que ya tiene. */
    public function enviarComprobante(Request $request, ComprobanteEstudianteService $comprobantes): JsonResponse
    {
        $datos = $request->validate(self::reglasComprobante() + ['id_ins' => 'required|integer']);

        try {
            $comprobantes->enviar($request->user(), (int) $datos['id_ins'], $datos, $request->file('comprobante'));
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Recibimos tu comprobante. Caja lo revisará y te avisaremos por correo.'], 201);
    }

    /** Inscripción a un programa de pago con el comprobante del primer pago. */
    public function inscribirConComprobante(Request $request, ComprobanteEstudianteService $comprobantes): JsonResponse
    {
        $datos = $request->validate(self::reglasComprobante() + [
            'id_imp'  => 'required|integer',
            'id_plan' => 'required|integer',
        ]);

        try {
            $res = $comprobantes->inscribirConComprobante($request->user(), (int) $datos['id_imp'], (int) $datos['id_plan'], $datos, $request->file('comprobante'));
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => "Te inscribiste en {$res['programa']}. Caja revisará tu comprobante y te avisaremos por correo.",
            ...$res,
        ], 201);
    }

    private static function reglasComprobante(): array
    {
        return [
            'monto'          => 'required|numeric|gt:0|max:1000000',
            'fecha_deposito' => 'required|date|before_or_equal:today',
            'metodo_pago'    => 'required|in:qr,transferencia,deposito',
            'tipo_banco_id'  => 'nullable|integer|exists:tipos_banco,id',
            'nro_boleta'     => 'nullable|string|max:100',
            'comprobante'    => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ];
    }
}
