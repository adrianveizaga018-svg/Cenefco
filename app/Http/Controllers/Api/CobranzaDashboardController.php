<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Shared\Kernel\Support\SqlCompat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CobranzaDashboardController extends Controller
{
    public function metrics(): JsonResponse
    {
        $hoy        = Carbon::today()->toDateString();
        $enCincoDias = Carbon::today()->addDays(5)->toDateString();

        // 1. Distribución de modalidades (Contado vs Cuotas)
        $modalidades = DB::table('t_inscripcion')
            ->selectRaw('SUM(CASE WHEN id_plan IS NULL THEN 1 ELSE 0 END) as al_contado')
            ->selectRaw('SUM(CASE WHEN id_plan IS NOT NULL THEN 1 ELSE 0 END) as en_cuotas')
            ->where('estado', 1)
            ->first();

        // 2. Cuotas próximas a vencer (entre hoy y en 5 días, pendientes)
        $proximosAVencer = DB::table('t_inscripcion_cuotas as ic')
            ->join('t_inscripcion as ins', 'ic.id_ins', '=', 'ins.id_ins')
            ->join('t_usuario as u', 'ins.id_us', '=', 'u.id_us')
            ->where('ic.estado', '!=', 'pagado')
            ->whereBetween('ic.fecha_vencimiento', [$hoy, $enCincoDias])
            ->where('ins.estado', 1)
            ->selectRaw("
                ic.id,
                u.ci,
                u.celular,
                " . SqlCompat::trimConcat("COALESCE(u.nombre,'')", "' '", "COALESCE(u.appaterno,'')") . " as estudiante_nombre,
                ic.descripcion,
                ic.monto_a_pagar as monto,
                ic.fecha_vencimiento
            ")
            ->orderBy('ic.fecha_vencimiento', 'asc')
            ->get();

        // 3. Cuotas vencidas (fecha_vencimiento < hoy, no pagadas)
        $vencidos = DB::table('t_inscripcion_cuotas as ic')
            ->join('t_inscripcion as ins', 'ic.id_ins', '=', 'ins.id_ins')
            ->join('t_usuario as u', 'ins.id_us', '=', 'u.id_us')
            ->where('ic.estado', '!=', 'pagado')
            ->where('ic.fecha_vencimiento', '<', $hoy)
            ->where('ins.estado', 1)
            ->selectRaw("
                ic.id,
                u.ci,
                u.celular,
                " . SqlCompat::trimConcat("COALESCE(u.nombre,'')", "' '", "COALESCE(u.appaterno,'')") . " as estudiante_nombre,
                ic.descripcion,
                ic.monto_a_pagar as monto,
                ic.fecha_vencimiento
            ")
            ->orderBy('ic.fecha_vencimiento', 'asc')
            ->get();

        return response()->json([
            'modalidades' => [
                'al_contado' => (int) ($modalidades->al_contado ?? 0),
                'en_cuotas'  => (int) ($modalidades->en_cuotas  ?? 0),
            ],
            'alertas' => [
                'proximos_vencer' => $proximosAVencer,
                'vencidos'        => $vencidos,
            ],
        ]);
    }
}
