<?php

namespace App\Http\Controllers\Api;

use App\Application\Inscripciones\Services\InscripcionCuotasService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * Lo pagado de cada cuota lo calcula InscripcionCuotasService::sincronizar repartiendo
 * los pagos de la inscripción; aquí solo se registra el pago y se recalcula.
 * Saldo de una cuota = monto_a_pagar - monto_pagado.
 */
class CobranzaController extends Controller
{
    public function __construct(private readonly InscripcionCuotasService $cuotas) {}

    // Para modulo de Caja
    public function cuotasPendientes(string $ci): JsonResponse
    {
        // Antes solo encontraba a los registrados en Caja (tipoestudiante = 2);
        // los inscritos por el portal también deben poder pagar sus cuotas.
        $estudiante = DB::table('t_usuario')
            ->where('ci', trim($ci))
            ->orderByRaw("CASE WHEN tipoestudiante = '2' THEN 0 ELSE 1 END")
            ->orderBy('id_us_reg')
            ->first(['id_us', 'nombre', 'appaterno', 'apmaterno', 'ci', 'email', 'celular']);

        if (!$estudiante) {
            return response()->json(['error' => 'Estudiante no encontrado'], 404);
        }

        $this->cuotas->sincronizarEstudiante((int) $estudiante->id_us);

        $cuotas = DB::table('t_inscripcion_cuotas as ic')
            ->join('t_inscripcion as i', 'i.id_ins', '=', 'ic.id_ins')
            ->join('t_programa as p', 'p.id_imp', '=', 'i.id_imp')
            ->join('t_plan as pl', 'pl.id_plan', '=', 'i.id_plan')
            ->where('i.id_us', $estudiante->id_us)
            ->where('i.estado', 1) // no cobrar cuotas de inscripciones dadas de baja
            ->where('ic.estado', '!=', 'pagado')
            ->orderBy('ic.fecha_vencimiento', 'asc')
            ->select(
                'ic.id',
                'ic.id_ins',
                'ic.nro_cuota',
                'ic.descripcion',
                // Lo que falta pagar de la cuota (una cuota parcial cobra solo su saldo)
                DB::raw("(CAST(ic.monto_a_pagar AS DECIMAL(12,2)) - COALESCE(ic.monto_pagado, 0)) as monto_a_pagar"),
                'ic.monto_a_pagar as monto_original',
                'ic.monto_pagado',
                'ic.fecha_vencimiento',
                'ic.estado',
                'p.nombre_programa',
                'pl.titulo as nombre_plan',
                'pl.qr_image_url'
            )
            ->get()
            ->map(fn ($c) => $this->conQr($c));

        return response()->json([
            'estudiante' => $estudiante,
            'cuotas' => $cuotas,
        ]);
    }

    public function registrarPagoCuota(Request $request): JsonResponse
    {
        $request->validate([
            'id_cuota' => 'required|integer',
            'monto_pagado' => 'required|numeric|gt:0',
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

        // Validar boleta duplicada
        $boletaCheck = \App\Shared\Kernel\Support\BoletaValidator::verificar($request->nro_boleta);
        if ($boletaCheck['duplicada']) {
            return response()->json(array_merge(['message' => $boletaCheck['message']], $boletaCheck['datos'] ?? []), 422);
        }

        $inscripcion = DB::table('t_inscripcion')->where('id_ins', $cuota->id_ins)->first();

        $comprobanteUrl = null;
        if ($request->hasFile('comprobante')) {
            $comprobanteUrl = $request->file('comprobante')->store('pagos/cuotas', 'public');
        }

        $respuesta = DB::transaction(function () use ($request, $cuota, $inscripcion, $comprobanteUrl) {
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

            DB::table('t_inscripcion_cuotas')->where('id', $cuota->id)->update(['id_pago' => $idPago, 'updated_at' => now()]);
            $this->cuotas->sincronizar((int) $inscripcion->id_ins);

            return response()->json(['mensaje' => 'Pago de cuota registrado', 'id_pago' => $idPago]);
        });

        app(\App\Application\Usuarios\Services\PromocionEstudianteService::class)->promoverPorIdUs($inscripcion->id_us);
        \App\Application\Crm\Services\CrmService::alInscribir((int) $inscripcion->id_ins);

        return $respuesta;
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
                DB::raw("SUM(COALESCE(ic.monto_pagado, 0)) as total_cobrado"),
                DB::raw("SUM((CAST(ic.monto_a_pagar AS DECIMAL(12,2)) - COALESCE(ic.monto_pagado, 0))) as total_pendiente"),
                // Próxima cuota
                DB::raw("MIN(CASE WHEN ic.estado != 'pagado' THEN ic.fecha_vencimiento END) as proxima_vencimiento"),
                DB::raw("MIN(CASE WHEN ic.estado != 'pagado' THEN (CAST(ic.monto_a_pagar AS DECIMAL(12,2)) - COALESCE(ic.monto_pagado, 0)) END) as proxima_monto"),
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
            ->orderByRaw("CASE WHEN MIN(CASE WHEN ic.estado != 'pagado' THEN ic.fecha_vencimiento END) IS NULL THEN 1 ELSE 0 END")
            ->orderByRaw("MIN(CASE WHEN ic.estado != 'pagado' THEN ic.fecha_vencimiento END) ASC")
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
                    'ic.monto_a_pagar', 'ic.monto_pagado', DB::raw("(CAST(ic.monto_a_pagar AS DECIMAL(12,2)) - COALESCE(ic.monto_pagado, 0)) as saldo"),
                    'ic.fecha_vencimiento', 'ic.estado',
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
        $this->cuotas->sincronizar($idIns);

        $cuotas = DB::table('t_inscripcion_cuotas as ic')
            ->leftJoin('t_pago as p', 'p.id_pago', '=', 'ic.id_pago')
            ->leftJoin('tipos_banco as tb', 'tb.id', '=', 'p.tipo_banco_id')
            ->leftJoin('t_inscripcion as ins', 'ins.id_ins', '=', 'ic.id_ins')
            ->leftJoin('t_plan as pl', 'pl.id_plan', '=', 'ins.id_plan')
            ->where('ic.id_ins', $idIns)
            ->select(
                'ic.id', 'ic.nro_cuota', 'ic.descripcion',
                'ic.monto_a_pagar', 'ic.monto_pagado', DB::raw("(CAST(ic.monto_a_pagar AS DECIMAL(12,2)) - COALESCE(ic.monto_pagado, 0)) as saldo"),
                'ic.fecha_vencimiento', 'ic.estado',
                'p.metodo_pago', 'p.nro_boleta_bancaria',
                'p.fecha_deposito', 'p.comprobante_url',
                'p.monto_pagado as monto_pagado_real',
                'tb.nombre as tipo_banco_nombre',
                'pl.qr_image_url',
            )
            ->orderBy('ic.nro_cuota')
            ->get()
            ->map(fn ($c) => $this->conQr($c));

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

        // date('now') solo existía en SQLite: la fecha va como parámetro
        $res = (clone $base)->where('i.estado', 1)->selectRaw("
            SUM(CASE WHEN ic.estado != 'pagado' AND ic.fecha_vencimiento < ? THEN (CAST(ic.monto_a_pagar AS DECIMAL(12,2)) - COALESCE(ic.monto_pagado, 0)) ELSE 0 END) as total_vencido,
            SUM((CAST(ic.monto_a_pagar AS DECIMAL(12,2)) - COALESCE(ic.monto_pagado, 0))) as total_pendiente,
            SUM(COALESCE(ic.monto_pagado, 0)) as total_cobrado,
            COUNT(CASE WHEN ic.estado != 'pagado' THEN 1 END) as cuotas_pendientes,
            COUNT(CASE WHEN ic.estado = 'pagado' THEN 1 END) as cuotas_pagadas
        ", [now()->toDateString()])->first();

        return response()->json([
            'total_vencido'    => (float) ($res->total_vencido ?? 0),
            'total_pendiente'  => (float) ($res->total_pendiente ?? 0),
            'total_cobrado'    => (float) ($res->total_cobrado ?? 0),
            'cuotas_pendientes' => (int) ($res->cuotas_pendientes ?? 0),
            'cuotas_pagadas'   => (int) ($res->cuotas_pagadas ?? 0),
        ]);
    }

    // ── Pagar cuota desde dashboard (ruta: POST /cobranzas/cuotas/{id}/pagar) ──

    public function pagarCuota(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'monto_pagado'   => 'required|numeric|gt:0',
            'nro_boleta'     => 'nullable|string|max:200',
            'fecha_deposito' => 'required|date',
            'metodo_pago'    => 'required|string',
            'tipo_banco_id'  => 'nullable|integer',
            'comprobante'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $cuota = DB::table('t_inscripcion_cuotas')->where('id', $id)->first();
        if (!$cuota) {
            return response()->json(['message' => 'Cuota no encontrada'], 404);
        }
        if ($cuota->estado === 'pagado') {
            return response()->json(['message' => 'Esta cuota ya fue pagada'], 422);
        }

        // Validar boleta duplicada
        $boletaCheck = \App\Shared\Kernel\Support\BoletaValidator::verificar($request->input('nro_boleta'));
        if ($boletaCheck['duplicada']) {
            return response()->json(
                array_merge(['message' => $boletaCheck['message']], $boletaCheck['datos'] ?? []),
                422
            );
        }

        $inscripcion = DB::table('t_inscripcion')->where('id_ins', $cuota->id_ins)->first();

        $comprobanteUrl = null;
        if ($request->hasFile('comprobante')) {
            $comprobanteUrl = $request->file('comprobante')->store('pagos/cuotas', 'public');
        }

        $respuesta = DB::transaction(function () use ($request, $cuota, $inscripcion, $comprobanteUrl, $id) {
            $cajero = $request->user();
            $idPago = (DB::table('t_pago')->max('id_pago') ?? 0) + 1;

            DB::table('t_pago')->insert([
                'id_pago'             => $idPago,
                'id_us_reg'           => $cajero->id,
                'id_us'               => $inscripcion->id_us,
                'id_ins'              => $inscripcion->id_ins,
                'monto_pagado'        => $request->input('monto_pagado'),
                'nro_boleta_bancaria' => $request->input('nro_boleta'),
                'fecha_deposito'      => $request->input('fecha_deposito'),
                'metodo_pago'         => $request->input('metodo_pago'),
                'tipo_banco_id'       => $request->input('tipo_banco_id'),
                'estado'              => 1,
                'estado_verificacion' => 'verificado',
                'nota_verificacion'   => 'Pago de cuota registrado desde cobranzas por ' . $cajero->nombre,
                'fecha_reg'           => now(),
                'pago_extra'          => 0,
                'comprobante_url'     => $comprobanteUrl,
            ]);

            DB::table('t_inscripcion_cuotas')->where('id', $id)->update(['id_pago' => $idPago, 'updated_at' => now()]);
            $this->cuotas->sincronizar((int) $inscripcion->id_ins);

            return response()->json([
                'mensaje'  => 'Pago de cuota registrado correctamente',
                'id_pago'  => $idPago,
                'id_cuota' => $id,
            ]);
        });

        app(\App\Application\Usuarios\Services\PromocionEstudianteService::class)->promoverPorIdUs($inscripcion->id_us);
        \App\Application\Crm\Services\CrmService::alInscribir((int) $inscripcion->id_ins);

        return $respuesta;
    }

    /** QR de cobro del plan, listo para mostrarse en el panel (la ruta guardada es relativa al storage). */
    private function conQr(object $cuota): object
    {
        $ruta = trim((string) ($cuota->qr_image_url ?? ''));
        $cuota->qr_url = match (true) {
            $ruta === '' => null,
            str_starts_with($ruta, 'http') => parse_url($ruta, PHP_URL_PATH) ?: $ruta,
            default => '/storage/'.ltrim(preg_replace('#^/?storage/#', '', $ruta), '/'),
        };
        unset($cuota->qr_image_url);

        return $cuota;
    }
}
