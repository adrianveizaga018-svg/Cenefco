<?php

namespace App\Application\Inscripciones\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InscripcionCuotasService
{
    public function generarCuotasParaInscripcion(int $idIns, int $idPlan, ?int $idPagoInicial = null): void
    {
        $templates = DB::table('t_fechapago')
            ->where('id_plan', $idPlan)
            ->where('estado', 1)
            ->orderBy('nro_pago')
            ->get();

        if ($templates->isEmpty()) {
            return;
        }

        $plan = DB::table('t_plan')->where('id_plan', $idPlan)->first();
        $modoFechas = $plan->modo_fechas ?? 'fijo';
        $fechaIns = now();

        $cuotasAInsertar = [];
        $pagoAsignado = false;

        foreach ($templates as $i => $t) {
            $fechaVencimiento = null;
            if ($modoFechas === 'relativo' && isset($t->dias_desde_inscripcion) && $t->dias_desde_inscripcion !== null) {
                // Plan "desde que se inscribe": calcular fecha relativa a la inscripción
                $fechaVencimiento = $fechaIns->copy()->addDays((int) $t->dias_desde_inscripcion)->format('Y-m-d');
            } else {
                // Plan "fecha fija": usar la fecha límite absoluta del template
                $fechaVencimiento = $t->fecha_fin;
            }

            $estado = 'pendiente';
            $idPago = null;

            // Asignar el pago inicial a la primera cuota (asumiendo que paga la 1ra cuota)
            if (!$pagoAsignado && $idPagoInicial) {
                $estado = 'pagado';
                $idPago = $idPagoInicial;
                $pagoAsignado = true;
            }

            $cuotasAInsertar[] = [
                'id_ins' => $idIns,
                'id_fechapago_template' => $t->id_fechapago,
                'nro_cuota' => $t->nro_pago ?: ($i + 1),
                'descripcion' => $t->tipo_tramite ?: ('Cuota ' . ($t->nro_pago ?: ($i + 1))),
                'monto_a_pagar' => $t->monto_a_pagar,
                'fecha_vencimiento' => $fechaVencimiento,
                'estado' => $estado,
                'id_pago' => $idPago,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('t_inscripcion_cuotas')->insert($cuotasAInsertar);
    }
}
