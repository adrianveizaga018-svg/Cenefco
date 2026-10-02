<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Zoom\StoreZoomCuentaRequest;
use App\Http\Requests\Zoom\StoreZoomReunionRequest;
use App\Http\Requests\Zoom\UpdateZoomCuentaRequest;
use App\Services\ZoomApiService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ZoomController extends Controller
{
    public function __construct(private readonly ZoomApiService $zoom) {}

    public function cuentas(): JsonResponse
    {
        $cuentas = DB::table('zoom_cuentas')
            ->orderByDesc('predeterminada')
            ->orderBy('nombre')
            ->get()
            ->map(fn ($c) => [
                'id'             => $c->id,
                'nombre'         => $c->nombre,
                'descripcion'    => $c->descripcion,
                'account_id'     => $c->account_id,
                'timezone'       => $c->timezone,
                'predeterminada' => (bool) $c->predeterminada,
                'activa'         => (bool) $c->activa,
                'created_at'     => $c->created_at,
            ]);

        return response()->json(['data' => $cuentas, 'total' => $cuentas->count()]);
    }

    public function storeCuenta(StoreZoomCuentaRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['timezone']      ??= 'America/La_Paz';
        $data['activa']          = $request->boolean('activa', true);
        $data['predeterminada']  = false;
        $data['created_at']      = $data['updated_at'] = now();

        $id = DB::table('zoom_cuentas')->insertGetId($data);

        return response()->json(DB::table('zoom_cuentas')->find($id), 201);
    }

    public function updateCuenta(UpdateZoomCuentaRequest $request, int $id): JsonResponse
    {
        if (! DB::table('zoom_cuentas')->where('id', $id)->exists()) {
            abort(404, 'Cuenta no encontrada');
        }

        $data = $request->validated();
        $data['updated_at'] = now();

        DB::table('zoom_cuentas')->where('id', $id)->update($data);

        return response()->json(DB::table('zoom_cuentas')->find($id));
    }

    public function destroyCuenta(int $id): JsonResponse
    {
        DB::table('zoom_cuentas')->where('id', $id)->delete();

        return response()->json(null, 204);
    }

    public function setPredeterminada(int $id): JsonResponse
    {
        if (! DB::table('zoom_cuentas')->where('id', $id)->exists()) {
            abort(404);
        }

        DB::table('zoom_cuentas')->update(['predeterminada' => false]);
        DB::table('zoom_cuentas')->where('id', $id)->update(['predeterminada' => true]);

        return response()->json(['message' => 'Cuenta predeterminada actualizada']);
    }

    public function testCuenta(int $id): JsonResponse
    {
        try {
            $reuniones = $this->zoom->usarCuenta($id)->listarReuniones();

            return response()->json(['ok' => true, 'reuniones' => count($reuniones)]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 502);
        }
    }

    private function zoomParaRequest(Request $request): ZoomApiService
    {
        $cuentaId = $request->integer('cuenta_id', 0);

        return $cuentaId > 0 ? $this->zoom->usarCuenta($cuentaId) : $this->zoom;
    }

    public function meetings(Request $request): JsonResponse
    {
        try {
            return response()->json(['meetings' => $this->zoomParaRequest($request)->listarReuniones()]);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Error al conectar con Zoom: '.$e->getMessage()], 502);
        }
    }

    public function crearReunion(StoreZoomReunionRequest $request): JsonResponse
    {
        $data  = $request->validated();
        $zoom  = $this->zoomParaRequest($request);
        $duracion = (int) ($data['duracion_min'] ?? 60);

        try {
            if ($data['tipo'] === 'unica') {
                $reunion = $zoom->crearReunion($data['tema'], $data['fecha_inicio'], $duracion);

                return response()->json(['tipo' => 'unica', 'reunion' => $reunion], 201);
            }

            $sesiones  = [];
            $fechaDt   = Carbon::parse($data['fecha_inicio']);
            $diasEntre = (int) ($data['dias_entre'] ?? 7);
            $n         = (int) $data['n_sesiones'];

            for ($i = 1; $i <= $n; $i++) {
                $tema     = "{$data['curso']} — Sesión {$i}";
                $fecha    = $fechaDt->copy()->addDays($diasEntre * ($i - 1))->format('Y-m-d\TH:i:s');
                $sesiones[] = $zoom->crearReunion($tema, $fecha, $duracion);
            }

            return response()->json(['tipo' => 'multisesion', 'sesiones' => $sesiones], 201);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Error al crear reunión en Zoom: '.$e->getMessage()], 502);
        }
    }

    public function recordings(Request $request): JsonResponse
    {
        try {
            return response()->json(['recordings' => $this->zoomParaRequest($request)->grabaciones()]);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Error al obtener grabaciones: '.$e->getMessage()], 502);
        }
    }
}
