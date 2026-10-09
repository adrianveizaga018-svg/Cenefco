<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Shared\Kernel\Support\SqlCompat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Listado general de devoluciones (todas las inscripciones) para revisarlas y resolverlas. */
class DevolucionesPanelController extends Controller
{
    /** GET /devoluciones?estado=pendiente|aprobada|rechazada|cancelada|todas */
    public function index(Request $request): JsonResponse
    {
        $estado = (string) $request->query('estado', 'pendiente');

        $filas = DB::table('web_devolucion as d')
            ->leftJoin('t_inscripcion as ins', 'ins.id_ins', '=', 'd.id_ins')
            ->leftJoin('t_imparte as imp', 'imp.id_imp', '=', 'ins.id_imp')
            ->leftJoin('t_programa as prog', 'prog.id_programa', '=', 'imp.id_mat')
            ->leftJoin('usuarios as reg', 'reg.id', '=', 'd.registrado_por')
            ->when($estado !== 'todas', fn ($q) => $q->where('d.estado', $estado))
            ->orderByRaw("CASE d.estado WHEN 'pendiente' THEN 0 ELSE 1 END")
            ->orderByDesc('d.created_at')
            ->limit(300)
            ->get([
                'd.id', 'd.id_ins', 'd.id_us', 'd.monto', 'd.motivo', 'd.estado', 'd.nota_respuesta',
                'd.fecha_respuesta', 'd.documento_url', 'd.created_at',
                'prog.nombre_programa as programa',
                DB::raw(SqlCompat::trimConcat("COALESCE(reg.nombre,'')", "' '", "COALESCE(reg.apellido,'')").' as registrado_por_nombre'),
            ]);

        // Nombre y carnet del estudiante (t_usuario tiene una fila por registro: se toma la primera)
        $estudiantes = DB::table('t_usuario')
            ->whereIn('id_us', $filas->pluck('id_us')->filter()->unique())
            ->orderBy('id_us_reg')
            ->get(['id_us', 'nombre', 'appaterno', 'apmaterno', 'ci', 'celular'])
            ->unique('id_us')
            ->keyBy('id_us');

        $data = $filas->map(function ($d) use ($estudiantes) {
            $e = $estudiantes[$d->id_us] ?? null;

            return [
                ...(array) $d,
                'monto'      => (float) $d->monto,
                'estudiante' => $e ? trim("{$e->nombre} {$e->appaterno} {$e->apmaterno}") : null,
                'ci'         => $e->ci ?? null,
                'celular'    => $e->celular ?? null,
            ];
        });

        return response()->json([
            'data'              => $data,
            'pendientes'        => DB::table('web_devolucion')->where('estado', 'pendiente')->count(),
            'monto_pendiente'   => (float) DB::table('web_devolucion')->where('estado', 'pendiente')->sum('monto'),
        ]);
    }
}
