<?php

namespace App\Http\Controllers\Api;

use App\Application\Inscripciones\Services\InscripcionCuotasService;
use App\Http\Controllers\Controller;
use App\Shared\Kernel\Support\SqlCompat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferenciaInscripcionController extends Controller
{
    public function __construct(
        private readonly InscripcionCuotasService $cuotasSvc,
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
            'id_plan_destino' => ['required', 'integer'],
        ]);

        [$resumen, $error] = $this->calcularTransferencia($id, (int) $data['id_plan_destino']);
        if ($error) {
            return response()->json(['message' => $error], 422);
        }

        return response()->json($resumen);
    }

    // ── 4. Ejecutar transferencia ─────────────────────────────────────────

    public function transferir(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'id_imp_destino'  => ['required', 'integer'],
            'id_plan_destino' => ['required', 'integer'],
            'motivo'          => ['nullable', 'string', 'max:500'],
        ]);

        $inscripcionOrigen = DB::table('t_inscripcion')->where('id_ins', $id)->first();
        if (! $inscripcionOrigen) {
            return response()->json(['message' => 'Inscripción no encontrada.'], 404);
        }
        if ((int) $inscripcionOrigen->estado !== 1) {
            return response()->json(['message' => 'Solo se pueden transferir inscripciones activas.'], 422);
        }

        [$resumen, $error] = $this->calcularTransferencia($id, (int) $data['id_plan_destino']);
        if ($error) {
            return response()->json(['message' => $error], 422);
        }

        return DB::transaction(function () use ($id, $data, $inscripcionOrigen, $resumen) {
            $cajero    = auth()->user();
            $idUs      = (int) $inscripcionOrigen->id_us;
            $motivo    = trim($data['motivo'] ?? 'Cambio de programa');
            $idImpDest = (int) $data['id_imp_destino'];
            $idPlanDest = (int) $data['id_plan_destino'];

            // 1. Dar de baja la inscripción origen
            DB::table('t_inscripcion')->where('id_ins', $id)->update([
                'estado'          => 0,
                'observacion_ins' => trim(($inscripcionOrigen->observacion_ins ?? '')
                    . "\n[TRANSFERIDO] Motivo: {$motivo}"),
            ]);

            // 2. Crear nueva inscripción en el destino
            $idInsNuevo = (DB::table('t_inscripcion')->max('id_ins') ?? 0) + 1;
            DB::table('t_inscripcion')->insert([
                'id_ins'      => $idInsNuevo,
                'id_us_reg'   => $cajero->id,
                'id_us'       => $idUs,
                'id_imp'      => $idImpDest,
                'id_plan'     => $idPlanDest,
                'fecha_ins'   => now()->toDateString(),
                'fecha_reg'   => now(),
                'gestion'     => now()->year,
                'periodo'     => 'I-' . now()->year,
                'estado'      => 1,
                'canal_venta' => 'transferencia',
                'origen'      => 'transferencia_programa',
                'observacion_ins' => "[TRANSFERIDO DESDE ins #{$id}] Motivo: {$motivo}",
            ]);

            // 3. Generar cuotas del nuevo plan aplicando distribución automática
            $this->generarCuotasTransferidas(
                $idInsNuevo,
                $idPlanDest,
                $resumen['distribucion'],
                $cajero->id,
                $id,
                $motivo
            );

            // 4. Registrar la transferencia en el historial
            DB::table('transferencias_inscripcion')->insert([
                'id_ins_origen'    => $id,
                'id_ins_destino'   => $idInsNuevo,
                'id_us'            => $idUs,
                'monto_transferido' => (float) $resumen['total_pagado_origen'],
                'motivo'           => $motivo,
                'procesado_por'    => $cajero->id,
                'created_at'       => now(),
            ]);

            return response()->json([
                'message'        => 'Transferencia realizada correctamente.',
                'id_ins_origen'  => $id,
                'id_ins_destino' => $idInsNuevo,
                'distribucion'   => $resumen['distribucion'],
            ], 201);
        });
    }

    // ── Helpers privados ──────────────────────────────────────────────────

    /**
     * Calcula cómo se distribuye el dinero pagado en origen sobre las cuotas del destino.
     * No modifica nada en BD.
     *
     * @return array{0: array|null, 1: string|null}  [resumen, error]
     */
    private function calcularTransferencia(int $idInsOrigen, int $idPlanDestino): array
    {
        $inscripcion = DB::table('t_inscripcion')->where('id_ins', $idInsOrigen)->first();
        if (! $inscripcion) {
            return [null, 'Inscripción no encontrada.'];
        }

        // Total pagado en la inscripción origen
        $totalPagado = (float) DB::table('t_inscripcion_cuotas')
            ->where('id_ins', $idInsOrigen)
            ->where('estado', 'pagado')
            ->sum(DB::raw('CAST(monto_a_pagar AS DECIMAL(12,2))'));

        // Si no tiene cuotas generadas, buscar en t_pago directo
        if ($totalPagado == 0.0) {
            $totalPagado = (float) DB::table('t_pago')
                ->where('id_ins', $idInsOrigen)
                ->where('estado', 1)
                ->sum(DB::raw('CAST(monto_pagado AS DECIMAL(12,2))'));
        }

        // Cuotas del plan destino
        $cuotasDestino = DB::table('t_fechapago')
            ->where('id_plan', $idPlanDestino)
            ->where('estado', 1)
            ->orderBy('nro_pago')
            ->get(['id_fechapago', 'nro_pago', 'tipo_tramite', 'monto_a_pagar', 'fecha_fin', 'dias_desde_inscripcion']);

        if ($cuotasDestino->isEmpty()) {
            // Plan de contado (1 cuota o sin cuotas definidas)
            $planDestino = DB::table('t_plan')->where('id_plan', $idPlanDestino)->first();
            $costoTotal  = $planDestino ? (float) $planDestino->costo : 0.0;

            $distribucion = [[
                'nro_cuota'       => 1,
                'descripcion'     => 'Pago único',
                'monto_cuota'     => $costoTotal,
                'monto_acreditado' => min($totalPagado, $costoTotal),
                'monto_pendiente' => max(0.0, $costoTotal - $totalPagado),
                'estado'          => $totalPagado >= $costoTotal ? 'pagado' : ($totalPagado > 0 ? 'parcial' : 'pendiente'),
                'fecha_vencimiento' => null,
            ]];

            return [[
                'total_pagado_origen' => $totalPagado,
                'total_nuevo_plan'    => $costoTotal,
                'distribucion'        => $distribucion,
            ], null];
        }

        // Distribución proporcional: acumular dinero disponible y aplicar cuota por cuota
        $disponible   = $totalPagado;
        $distribucion = [];

        foreach ($cuotasDestino as $cuota) {
            $montoCuota = (float) $cuota->monto_a_pagar;

            if ($disponible >= $montoCuota) {
                $acreditado = $montoCuota;
                $pendiente  = 0.0;
                $estado     = 'pagado';
            } elseif ($disponible > 0) {
                $acreditado = round($disponible, 2);
                $pendiente  = round($montoCuota - $disponible, 2);
                $estado     = 'parcial';
            } else {
                $acreditado = 0.0;
                $pendiente  = $montoCuota;
                $estado     = 'pendiente';
            }

            $disponible -= $acreditado;

            $distribucion[] = [
                'id_fechapago'    => $cuota->id_fechapago,
                'nro_cuota'       => $cuota->nro_pago,
                'descripcion'     => $cuota->tipo_tramite ?? ('Cuota ' . $cuota->nro_pago),
                'monto_cuota'     => $montoCuota,
                'monto_acreditado' => $acreditado,
                'monto_pendiente' => $pendiente,
                'estado'          => $estado,
                'dias_desde_inscripcion' => $cuota->dias_desde_inscripcion,
                'fecha_vencimiento' => $cuota->fecha_fin,
            ];
        }

        $costoTotal = $cuotasDestino->sum(fn ($c) => (float) $c->monto_a_pagar);

        return [[
            'total_pagado_origen' => $totalPagado,
            'total_nuevo_plan'    => $costoTotal,
            'distribucion'        => $distribucion,
        ], null];
    }

    /**
     * Genera las filas en t_inscripcion_cuotas para la nueva inscripción
     * aplicando los montos ya acreditados y creando pagos de transferencia.
     */
    private function generarCuotasTransferidas(
        int   $idInsNuevo,
        int   $idPlanDestino,
        array $distribucion,
        int   $procesadoPor,
        int   $idInsOrigen,
        string $motivo
    ): void {
        $plan      = DB::table('t_plan')->where('id_plan', $idPlanDestino)->first();
        $modoFechas = $plan->modo_fechas ?? 'fijo';
        $fechaIns   = now();

        foreach ($distribucion as $d) {
            // Calcular fecha de vencimiento según modo del plan
            $fechaVenc = null;
            if ($modoFechas === 'relativo' && isset($d['dias_desde_inscripcion'])) {
                $fechaVenc = $fechaIns->copy()->addDays((int) $d['dias_desde_inscripcion'])->format('Y-m-d');
            } else {
                $fechaVenc = $d['fecha_vencimiento'] ?? null;
            }

            $idPago = null;
            $estado = 'pendiente';

            // Si la cuota queda totalmente cubierta → crear un pago de transferencia y marcar pagada
            if ($d['estado'] === 'pagado' && $d['monto_acreditado'] > 0) {
                $idPago = (DB::table('t_pago')->max('id_pago') ?? 0) + 1;
                DB::table('t_pago')->insert([
                    'id_pago'             => $idPago,
                    'id_us_reg'           => $procesadoPor,
                    'id_us'               => DB::table('t_inscripcion')->where('id_ins', $idInsNuevo)->value('id_us'),
                    'id_ins'              => $idInsNuevo,
                    'monto_pagado'        => $d['monto_acreditado'],
                    'metodo_pago'         => 'transferencia_interna',
                    'nro_boleta_bancaria' => "TRANS-INS-{$idInsOrigen}",
                    'fecha_deposito'      => now()->toDateString(),
                    'estado'              => 1,
                    'estado_verificacion' => 'verificado',
                    'nota_verificacion'   => "Transferido desde inscripción #{$idInsOrigen}. Motivo: {$motivo}",
                    'fecha_reg'           => now(),
                    'pago_extra'          => 0,
                ]);
                $estado = 'pagado';
            }

            // Si es parcial → crear pago por el monto acreditado, pero la cuota sigue pendiente
            if ($d['estado'] === 'parcial' && $d['monto_acreditado'] > 0) {
                $idPago = (DB::table('t_pago')->max('id_pago') ?? 0) + 1;
                DB::table('t_pago')->insert([
                    'id_pago'             => $idPago,
                    'id_us_reg'           => $procesadoPor,
                    'id_us'               => DB::table('t_inscripcion')->where('id_ins', $idInsNuevo)->value('id_us'),
                    'id_ins'              => $idInsNuevo,
                    'monto_pagado'        => $d['monto_acreditado'],
                    'metodo_pago'         => 'transferencia_interna',
                    'nro_boleta_bancaria' => "TRANS-INS-{$idInsOrigen}",
                    'fecha_deposito'      => now()->toDateString(),
                    'estado'              => 1,
                    'estado_verificacion' => 'verificado',
                    'nota_verificacion'   => "Pago parcial transferido desde inscripción #{$idInsOrigen}. Motivo: {$motivo}",
                    'fecha_reg'           => now(),
                    'pago_extra'          => 1, // marcado como anticipo/pago extra
                ]);
                $estado  = 'pendiente'; // cuota aún pendiente (pago parcial)
                $idPago  = null;        // no se vincula a la cuota
            }

            DB::table('t_inscripcion_cuotas')->insert([
                'id_ins'                 => $idInsNuevo,
                'id_fechapago_template'  => $d['id_fechapago'] ?? null,
                'nro_cuota'              => $d['nro_cuota'],
                'descripcion'            => $d['descripcion'],
                'monto_a_pagar'          => $d['monto_cuota'],
                'fecha_vencimiento'      => $fechaVenc,
                'estado'                 => $estado,
                'id_pago'                => $idPago,
                'created_at'             => now(),
                'updated_at'             => now(),
            ]);
        }
    }
}
