<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CobranzaController extends Controller
{
    // Para modulo de Caja
    public function cuotasPendientes(string $ci): JsonResponse
    {
        $estudiante = DB::table('t_usuario')
            ->where('ci', $ci)
            ->where('tipoestudiante', '2')
            ->first(['id_us', 'nombre', 'appaterno', 'apmaterno', 'ci', 'email', 'celular']);

        if (!$estudiante) {
            return response()->json(['error' => 'Estudiante no encontrado'], 404);
        }

        $cuotas = DB::table('t_inscripcion_cuotas as ic')
            ->join('t_inscripcion as i', 'i.id_ins', '=', 'ic.id_ins')
            ->join('t_programa as p', 'p.id_imp', '=', 'i.id_imp')
            ->join('t_plan as pl', 'pl.id_plan', '=', 'i.id_plan')
            ->where('i.id_us', $estudiante->id_us)
            ->where('ic.estado', '!=', 'pagado')
            ->orderBy('ic.fecha_vencimiento', 'asc')
            ->select(
                'ic.id',
                'ic.id_ins',
                'ic.nro_cuota',
                'ic.descripcion',
                'ic.monto_a_pagar',
                'ic.fecha_vencimiento',
                'ic.estado',
                'p.nombre_programa',
                'pl.titulo as nombre_plan'
            )
            ->get();

        return response()->json([
            'estudiante' => $estudiante,
            'cuotas' => $cuotas,
        ]);
    }

    public function registrarPagoCuota(Request $request): JsonResponse
    {
        $request->validate([
            'id_cuota' => 'required|integer',
            'monto_pagado' => 'required|numeric|min:0',
            'nro_boleta' => 'nullable|string',
            'fecha_deposito' => 'required|date',
            'metodo_pago' => 'required|string',
            'tipo_banco_id' => 'nullable|integer',
            'comprobante' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $cuota = DB::table('t_inscripcion_cuotas')->where('id', $request->id_cuota)->first();
        if (!$cuota || $cuota->estado === 'pagado') {
            return response()->json(['error' => 'Cuota no válida o ya pagada'], 400);
        }

        $inscripcion = DB::table('t_inscripcion')->where('id_ins', $cuota->id_ins)->first();

        $comprobanteUrl = null;
        if ($request->hasFile('comprobante')) {
            $comprobanteUrl = $request->file('comprobante')->store('pagos/cuotas', 'public');
        }

        return DB::transaction(function () use ($request, $cuota, $inscripcion, $comprobanteUrl) {
            $cajero = $request->user();
            $idPago = (DB::table('t_pago')->max('id_pago') ?? 0) + 1;

            DB::table('t_pago')->insert([
                'id_pago'            => $idPago,
                'id_us_reg'          => $cajero->id,
                'id_us'              => $inscripcion->id_us,
                'id_ins'             => $inscripcion->id_ins,
                'monto_pagado'       => $request->monto_pagado,
                'nro_boleta_bancaria' => $request->nro_boleta,
                'fecha_deposito'     => $request->fecha_deposito,
                'metodo_pago'        => $request->metodo_pago,
                'tipo_banco_id'      => $request->tipo_banco_id,
                'estado'             => 1,
                'estado_verificacion' => 'verificado',
                'nota_verificacion'  => 'Pago de cuota en caja por ' . $cajero->nombre,
                'fecha_reg'          => now(),
                'pago_extra'         => 0,
                'comprobante_url'    => $comprobanteUrl,
            ]);

            DB::table('t_inscripcion_cuotas')->where('id', $cuota->id)->update([
                'estado' => 'pagado',
                'id_pago' => $idPago,
                'updated_at' => now(),
            ]);

            return response()->json(['mensaje' => 'Pago de cuota registrado', 'id_pago' => $idPago]);
        });
    }

    // Para modulo de Dashboard de Cobranzas — agrupado por inscripción
    public function dashboard(Request $request): JsonResponse
    {
        $query = DB::table('t_inscripcion_cuotas as ic')
            ->join('t_inscripcion as i', 'i.id_ins', '=', 'ic.id_ins')
            ->join('t_usuario as u', 'u.id_us', '=', 'i.id_us')
            ->leftJoin('t_imparte as imp', 'imp.id_imp', '=', 'i.id_imp')
            ->leftJoin(
                DB::raw('(SELECT id_imp, MIN(nombre_programa) as nombre_programa FROM t_programa GROUP BY id_imp) as prog'),
                'prog.id_imp', '=', 'i.id_imp'
            )
            ->leftJoin('t_pago as pago', 'pago.id_pago', '=', 'ic.id_pago')
            ->leftJoin('t_usuario as vend', 'vend.id_us', '=', 'i.id_vendedor')
            ->select(
                'i.id_ins',
                'i.id_us',
                'i.id_imp',
                'i.canal_venta',
                'i.id_vendedor',
                'u.nombre', 'u.appaterno', 'u.apmaterno', 'u.ci', 'u.celular',
                DB::raw("COALESCE(prog.nombre_programa, '') as nombre_programa"),
                DB::raw(\App\Shared\Kernel\Support\SqlCompat::trimConcat("COALESCE(vend.nombre,'')", "' '", "COALESCE(vend.appaterno,'')") . " as vendedor_nombre"),
                // Totales por inscripción
                DB::raw("COUNT(ic.id) as total_cuotas"),
                DB::raw("SUM(CASE WHEN ic.estado = 'pagado' THEN 1 ELSE 0 END) as cuotas_pagadas"),
                DB::raw("SUM(CASE WHEN ic.estado != 'pagado' THEN 1 ELSE 0 END) as cuotas_pendientes"),
                DB::raw("SUM(CASE WHEN ic.estado = 'pagado' THEN CAST(ic.monto_a_pagar AS DECIMAL(12,2)) ELSE 0 END) as total_cobrado"),
                DB::raw("SUM(CASE WHEN ic.estado != 'pagado' THEN CAST(ic.monto_a_pagar AS DECIMAL(12,2)) ELSE 0 END) as total_pendiente"),
                // Próxima cuota
                DB::raw("MIN(CASE WHEN ic.estado != 'pagado' THEN ic.fecha_vencimiento END) as proxima_vencimiento"),
                DB::raw("MIN(CASE WHEN ic.estado != 'pagado' THEN ic.monto_a_pagar END) as proxima_monto"),
            )
            ->groupBy(
                'i.id_ins', 'i.id_us', 'i.id_imp', 'i.canal_venta', 'i.id_vendedor',
                'u.nombre', 'u.appaterno', 'u.apmaterno', 'u.ci', 'u.celular',
                'prog.nombre_programa', 'vend.nombre', 'vend.appaterno'
            );

        // Filtros
        if ($request->filled('estado')) {
            if ($request->estado === 'pendiente') {
                $query->having(DB::raw("SUM(CASE WHEN ic.estado != 'pagado' THEN 1 ELSE 0 END)"), '>', 0);
            } elseif ($request->estado === 'pagado') {
                $query->having(DB::raw("SUM(CASE WHEN ic.estado != 'pagado' THEN 1 ELSE 0 END)"), '=', 0);
            }
        }
        if ($request->filled('ci')) {
            $query->where('u.ci', 'like', '%' . $request->ci . '%');
        }
        if ($request->filled('programa_id')) {
            $query->where('prog.nombre_programa', 'like', '%' . $request->get('programa_id') . '%');
        }
        if ($request->filled('id_vendedor')) {
            $query->where('i.id_vendedor', (int) $request->id_vendedor);
        }
        if ($request->filled('canal_venta')) {
            $query->where('i.canal_venta', $request->canal_venta);
        }

        $inscripciones = $query
            ->orderByRaw("MIN(CASE WHEN ic.estado != 'pagado' THEN ic.fecha_vencimiento END) ASC NULLS LAST")
            ->paginate($request->get('per_page', 20));

        // Para cada inscripción, cargar el detalle de cuotas (solo si se pide expand)
        $expand = $request->boolean('expand', false);
        if ($expand) {
            $insIds = collect($inscripciones->items())->pluck('id_ins')->all();
            $cuotasPorIns = DB::table('t_inscripcion_cuotas as ic')
                ->leftJoin('t_pago as p', 'p.id_pago', '=', 'ic.id_pago')
                ->whereIn('ic.id_ins', $insIds)
                ->select(
                    'ic.id', 'ic.id_ins', 'ic.nro_cuota', 'ic.descripcion',
                    'ic.monto_a_pagar', 'ic.fecha_vencimiento', 'ic.estado',
                    'p.metodo_pago', 'p.nro_boleta_bancaria', 'p.fecha_deposito',
                    'p.comprobante_url', 'p.monto_pagado as monto_pagado_real',
                )
                ->orderBy('ic.nro_cuota')
                ->get()
                ->groupBy('id_ins');

            $inscripciones->getCollection()->transform(function ($ins) use ($cuotasPorIns) {
                $ins->cuotas = $cuotasPorIns->get($ins->id_ins, collect())->values()->all();
                return $ins;
            });
        }

        return response()->json($inscripciones);
    }

    /** Cuotas de una inscripción específica para el acordeón del dashboard. */
    public function cuotasInscripcion(int $idIns): JsonResponse
    {
        $cuotas = DB::table('t_inscripcion_cuotas as ic')
            ->leftJoin('t_pago as p', 'p.id_pago', '=', 'ic.id_pago')
            ->leftJoin('tipos_banco as tb', 'tb.id', '=', 'p.tipo_banco_id')
            ->where('ic.id_ins', $idIns)
            ->select(
                'ic.id', 'ic.nro_cuota', 'ic.descripcion',
                'ic.monto_a_pagar', 'ic.fecha_vencimiento', 'ic.estado',
                'p.metodo_pago', 'p.nro_boleta_bancaria',
                'p.fecha_deposito', 'p.comprobante_url',
                'p.monto_pagado as monto_pagado_real',
                'tb.nombre as tipo_banco_nombre',
            )
            ->orderBy('ic.nro_cuota')
            ->get();

        return response()->json($cuotas);
    }

    /** Resumen de totales para el dashboard de cobranzas. */
    public function resumen(Request $request): JsonResponse
    {
        $base = DB::table('t_inscripcion_cuotas as ic')
            ->join('t_inscripcion as i', 'i.id_ins', '=', 'ic.id_ins')
            ->join('t_usuario as u', 'u.id_us', '=', 'i.id_us')
            ->leftJoin('t_programa as p', 'p.id_imp', '=', 'i.id_imp')
            ->leftJoin('t_pago as pago', 'pago.id_pago', '=', 'ic.id_pago');

        if ($request->filled('ci')) $base->where('u.ci', 'like', '%' . $request->ci . '%');
        if ($request->filled('programa_id')) $base->where('p.id_programa', (int) $request->programa_id);
        if ($request->filled('id_vendedor')) $base->where('i.id_vendedor', (int) $request->id_vendedor);
        if ($request->filled('canal_venta')) $base->where('i.canal_venta', $request->canal_venta);

        $res = (clone $base)->selectRaw("
            SUM(CASE WHEN ic.estado != 'pagado' AND ic.fecha_vencimiento < date('now') THEN ic.monto_a_pagar ELSE 0 END) as total_vencido,
            SUM(CASE WHEN ic.estado != 'pagado' THEN ic.monto_a_pagar ELSE 0 END) as total_pendiente,
            SUM(CASE WHEN ic.estado = 'pagado' THEN ic.monto_a_pagar ELSE 0 END) as total_cobrado,
            COUNT(CASE WHEN ic.estado != 'pagado' THEN 1 END) as cuotas_pendientes,
            COUNT(CASE WHEN ic.estado = 'pagado' THEN 1 END) as cuotas_pagadas
        ")->first();

        return response()->json([
            'total_vencido'    => (float) ($res->total_vencido ?? 0),
            'total_pendiente'  => (float) ($res->total_pendiente ?? 0),
            'total_cobrado'    => (float) ($res->total_cobrado ?? 0),
            'cuotas_pendientes' => (int) ($res->cuotas_pendientes ?? 0),
            'cuotas_pagadas'   => (int) ($res->cuotas_pagadas ?? 0),
        ]);
    }
}
