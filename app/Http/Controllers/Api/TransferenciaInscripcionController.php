<?php

namespace App\Http\Controllers\Api;

use App\Application\Inscripciones\Services\InscripcionCuotasService;
use App\Application\Inscripciones\Services\TransferenciaProgramaService;
use App\Http\Controllers\Controller;
use App\Notifications\CambioProgramaNotification;
use Illuminate\Support\Facades\Notification;
use App\Shared\Kernel\Support\SqlCompat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferenciaInscripcionController extends Controller
{
    public function __construct(
        private readonly InscripcionCuotasService $cuotasSvc,
        private readonly TransferenciaProgramaService $transferencias,
    ) {}

    // ── 1. Dar de baja ────────────────────────────────────────────────────

    public function darDeBaja(Request $request, int $id): JsonResponse
    {
        $inscripcion = DB::table('t_inscripcion')->where('id_ins', $id)->first();
        if (! $inscripcion) {
            return response()->json(['message' => 'Inscripción no encontrada.'], 404);
        }
        if ((int) $inscripcion->estado === 0) {
            return response()->json(['message' => 'La inscripción ya está dada de baja.'], 422);
        }

        $data = $request->validate([
            'motivo' => ['required', 'string', 'max:500'],
        ]);

        DB::table('t_inscripcion')->where('id_ins', $id)->update([
            'estado'          => 0,
            'observacion_ins' => trim(($inscripcion->observacion_ins ?? '') . "\n[BAJA] " . $data['motivo']),
        ]);

        return response()->json([
            'message'      => 'Inscripción dada de baja correctamente.',
            'id_ins'       => $id,
            'nuevo_estado' => 0,
        ]);
    }

    // ── 2. Reactivar ──────────────────────────────────────────────────────

    public function reactivar(Request $request, int $id): JsonResponse
    {
        $inscripcion = DB::table('t_inscripcion')->where('id_ins', $id)->first();
        if (! $inscripcion) {
            return response()->json(['message' => 'Inscripción no encontrada.'], 404);
        }
        if ((int) $inscripcion->estado === 1) {
            return response()->json(['message' => 'La inscripción ya está activa.'], 422);
        }

        // Verificar que no exista ya otra inscripción activa en la misma impartición
        $duplicada = DB::table('t_inscripcion')
            ->where('id_us',  $inscripcion->id_us)
            ->where('id_imp', $inscripcion->id_imp)
            ->where('estado', 1)
            ->where('id_ins', '!=', $id)
            ->exists();

        if ($duplicada) {
            return response()->json([
                'message' => 'El estudiante ya tiene una inscripción activa en este programa. Dar de baja la otra antes de reactivar.',
            ], 422);
        }

        DB::table('t_inscripcion')->where('id_ins', $id)->update(['estado' => 1]);

        return response()->json([
            'message'      => 'Inscripción reactivada correctamente.',
            'id_ins'       => $id,
            'nuevo_estado' => 1,
        ]);
    }

    // ── 3. Preview de transferencia (sin modificar nada) ──────────────────

    public function previewTransferencia(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'id_imp_destino'  => ['required', 'integer'],
            'id_plan_destino' => ['required', 'integer'],
        ]);

        try {
            return response()->json($this->transferencias->preview($id, (int) $data['id_imp_destino'], (int) $data['id_plan_destino']));
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    // ── 4. Ejecutar transferencia ─────────────────────────────────────────

    public function transferir(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'id_imp_destino'  => ['required', 'integer'],
            'id_plan_destino' => ['required', 'integer'],
            'motivo'          => ['required', 'string', 'min:5', 'max:500'],
        ], ['motivo.required' => 'Indica el motivo del cambio de programa.', 'motivo.min' => 'Describe un poco más el motivo del cambio.']);

        try {
            $resultado = $this->transferencias->ejecutar(
                $id, (int) $data['id_imp_destino'], (int) $data['id_plan_destino'], trim($data['motivo']), (int) auth()->id(),
            );
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        $this->avisarEstudiante($resultado);

        return response()->json([
            'message'        => 'Cambio de programa realizado.',
            'id_ins_origen'  => $resultado['id_ins_origen'],
            'id_ins_destino' => $resultado['id_ins_destino'],
            'id_devolucion'  => $resultado['id_devolucion'],
            'saldo_a_favor'  => $resultado['preview']['saldo_a_favor'],
            'distribucion'   => $resultado['preview']['distribucion'],
        ], 201);
    }

    /** Correo al estudiante con su nuevo programa y plan. Si falla, el cambio igual queda hecho. */
    private function avisarEstudiante(array $resultado): void
    {
        try {
            $datos = DB::table('t_inscripcion as ins')
                ->join('t_usuario as u', 'u.id_us', '=', 'ins.id_us')
                ->join('t_imparte as imp', 'imp.id_imp', '=', 'ins.id_imp')
                ->leftJoin('t_programa as prog', 'prog.id_programa', '=', 'imp.id_mat')
                ->whereIn('ins.id_ins', [$resultado['id_ins_origen'], $resultado['id_ins_destino']])
                ->get(['ins.id_ins', 'u.nombre', 'u.email', 'prog.nombre_programa'])
                ->keyBy('id_ins');

            $nuevo = $datos[$resultado['id_ins_destino']] ?? null;
            if (! $nuevo || ! filter_var($nuevo->email, FILTER_VALIDATE_EMAIL)) {
                return;
            }

            $p = $resultado['preview'];
            Notification::route('mail', $nuevo->email)->notify(new CambioProgramaNotification(
                nombre:           (string) $nuevo->nombre,
                programaAnterior: (string) ($datos[$resultado['id_ins_origen']]->nombre_programa ?? 'Programa anterior'),
                programaNuevo:    (string) ($nuevo->nombre_programa ?? 'Nuevo programa'),
                reconocido:       (float) $p['total_pagado_origen'],
                saldoPendiente:   (float) $p['saldo_pendiente'],
                saldoAFavor:      (float) $p['saldo_a_favor'],
                cuotas:           $p['distribucion'],
            ));
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
