<?php

namespace App\Shared\Kernel\Support;

use Illuminate\Support\Facades\DB;

/**
 * Un plan es gratuito cuando su costo es 0 y ninguna de sus cuotas cobra algo.
 * Con un plan así la inscripción no registra pago (Caja) y el estudiante
 * puede inscribirse solo desde su panel.
 */
final class PlanGratuito
{
    public static function es(?int $idPlan): bool
    {
        if (! $idPlan) {
            return false;
        }

        $costo = DB::table('t_plan')->where('id_plan', $idPlan)->value('costo');
        if (! self::costoCero($costo)) {
            return false;
        }

        return ! DB::table('t_fechapago')
            ->where('id_plan', $idPlan)
            ->where('estado', 1)
            ->whereRaw(SqlCompat::castDecimal('monto_a_pagar').' > 0')
            ->exists();
    }

    /** `costo` es texto en t_plan: null o vacío significa "sin precio cargado", no gratis. */
    public static function costoCero(mixed $costo): bool
    {
        return $costo !== null && trim((string) $costo) !== '' && is_numeric($costo) && (float) $costo == 0.0;
    }
}
