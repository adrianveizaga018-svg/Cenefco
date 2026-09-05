<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstudiantePortalController extends Controller
{
    /**
     * Devuelve el resumen para el dashboard del estudiante autenticado.
     */
    public function dashboard(Request $request): JsonResponse
    {
        $user = auth()->user();

        // Resolver id_us en t_usuario usando el CI del usuario autenticado
        $tUsuario = DB::table('t_usuario')
            ->where('ci', $user->ci)
            ->orderBy('id_us_reg')
            ->first();

        $idUs = $tUsuario?->id_us ?? null;

        // 1. Cursos activos
        $cursosActivosCount = $idUs
            ? DB::table('t_inscripcion as ins')
                ->join('t_imparte as imp', 'ins.id_imp', '=', 'imp.id_imp')
                ->where('ins.id_us', $idUs)
                ->where('ins.estado', 1)
                ->count()
            : 0;

        // 2. Certificados emitidos
        $certificadosCount = DB::table('t_certificado')
            ->where('usuario_id', $user->id)
            ->where('estado', 'generado')
            ->count();

        // 3. Grabaciones de zoom
        $grabacionesCount = $idUs
            ? DB::table('t_zoom_grabacion as g')
                ->join('t_zoom_reunion as r', 'g.reunion_id', '=', 'r.id')
                ->join('t_inscripcion as ins', 'r.imparte_id', '=', 'ins.id_imp')
                ->where('ins.id_us', $idUs)
                ->count()
            : 0;

        return response()->json([
            'usuario' => [
                'id'     => $user->id,
                'nombre' => trim(($user->nombre ?? '').' '.($user->apellido ?? '')),
                'email'  => $user->email,
                'ci'     => $user->ci ?? null,
            ],
            'resumen' => [
                'cursos_activos'  => $cursosActivosCount,
                'certificados'    => $certificadosCount,
                'clases_grabadas' => $grabacionesCount,
            ]
        ]);
    }

    /**
     * Devuelve la lista de cursos en los que esta inscrito el estudiante logueado.
     */
    public function misCursos(Request $request): JsonResponse
    {
        $user = auth()->user();

        // Resolver id_us usando CI
        $tUsuario = DB::table('t_usuario')
            ->where('ci', $user->ci)
            ->orderBy('id_us_reg')
            ->first();

        if (!$tUsuario) {
            return response()->json(['data' => []]);
        }

        $idUs = $tUsuario->id_us;

        $cursos = DB::table('t_inscripcion as ins')
            ->join('t_imparte as i', 'ins.id_imp', '=', 'i.id_imp')
            ->leftJoin('t_materia as m', 'i.id_mat', '=', 'm.id_mat')
            ->leftJoin('t_docente as d', 'i.id_docente', '=', 'd.id_docente')
            ->leftJoin('t_persona as p', 'd.id_per', '=', 'p.id_per')
            ->select(
                'ins.id_ins',
                'ins.fecha_ins',
                'ins.estado as estado_inscripcion',
                'i.id_imp',
                'm.nombre as nombre_materia',
                'i.titulo_personalizado',
                'i.imparte_fecha_inicio',
                'i.imparte_fecha_fin',
                'i.horas_academicas',
                DB::raw("TRIM(CONCAT(COALESCE(p.per_appaterno,''), ' ', COALESCE(p.per_apmaterno,''), ' ', COALESCE(p.per_nombre,''))) as nombre_docente")
            )
            ->where('ins.id_us', $idUs)
            ->orderBy('ins.fecha_ins', 'desc')
            ->get();

        $cursosConZoom = $cursos->map(function ($c) {
            $grabaciones = DB::table('t_zoom_grabacion as g')
                ->join('t_zoom_reunion as r', 'g.reunion_id', '=', 'r.id')
                ->select('g.id', 'r.tema', 'g.play_url', 'g.duracion', 'g.recording_start')
                ->where('r.imparte_id', $c->id_imp)
                ->orderBy('g.recording_start', 'desc')
                ->get();

            $c->grabaciones = $grabaciones;
            return $c;
        });

        return response()->json(['data' => $cursosConZoom]);
    }

    /**
     * Devuelve los certificados emitidos unicamente para el estudiante logueado.
     */
    public function misCertificados(Request $request): JsonResponse
    {
        $userId = auth()->id();

        $certificados = DB::table('t_certificado as c')
            ->leftJoin('t_imparte as i', 'c.imparte_id', '=', 'i.id_imp')
            ->select(
                'c.id',
                'c.nombre_en_certificado',
                'c.programa_en_certificado',
                'c.codigo_verificacion',
                'c.qr_url',
                'c.archivo_url',
                'c.estado',
                'c.created_at'
            )
            ->where('c.usuario_id', $userId)
            ->orderBy('c.created_at', 'desc')
            ->get();

        return response()->json(['data' => $certificados]);
    }

    public function misPagos(Request $request): JsonResponse
    {
        $user = auth()->user();

        // Resolver id_us usando CI para cruzar con el sistema académico legacy
        $tUsuario = DB::table('t_usuario')
            ->where('ci', $user->ci)
            ->orderBy('id_us_reg')
            ->first();

        if (!$tUsuario) {
            return response()->json(['data' => []]);
        }

        $idUs = $tUsuario->id_us;

        $inscripciones = DB::table('t_inscripcion as ins')
            ->leftJoin('t_imparte as imp', function ($j) {
                $j->on('ins.id_imp', '=', 'imp.id_imp')
                  ->whereRaw('imp.id_us_reg = (SELECT MIN(i2.id_us_reg) FROM t_imparte i2 WHERE i2.id_imp = imp.id_imp)');
            })
            ->leftJoin('t_programa as prog', function ($j) {
                $j->on('prog.id_imp', '=', 'ins.id_imp')
                  ->whereRaw('prog.id_us_reg = (SELECT MIN(p2.id_us_reg) FROM t_programa p2 WHERE p2.id_imp = prog.id_imp)');
            })
            ->leftJoin('t_materia as mat', function ($j) {
                $j->on('mat.id_mat', '=', 'imp.id_mat')
                  ->whereRaw('mat.id_us_reg = (SELECT MIN(m2.id_us_reg) FROM t_materia m2 WHERE m2.id_mat = mat.id_mat)');
            })
            ->where('ins.id_us', $idUs)
            ->where('ins.estado', 1)
            ->select([
                'ins.id_ins',
                'ins.id_us',
                'ins.id_imp',
                'ins.id_plan',
                'ins.periodo',
                'ins.gestion',
                'ins.fecha_ins',
                'ins.canal_venta',
                DB::raw('COALESCE(prog.nombre_programa, imp.titulo_personalizado, mat.nombre, mat.nombremat) as nombre_programa'),
                'prog.slug as programa_slug',
                'imp.fecha_inicio',
                'imp.fecha_fin',
            ])
            ->orderByDesc('ins.fecha_ins')
            ->limit(50)
            ->get();

        $planIds = $inscripciones->pluck('id_plan')->filter()->unique()->values()->all();

        $todasCuotas = count($planIds)
            ? DB::table('t_fechapago')
                ->whereIn('id_plan', $planIds)
                ->where('estado', 1)
                ->select('id_fechapago', 'id_plan', 'nro_pago', 'monto_a_pagar', 'fecha_pago as fecha_limite')
                ->orderBy('nro_pago')
                ->get()
                ->groupBy('id_plan')
            : collect();

        $todosPagos = DB::table('t_pago as p')
            ->leftJoin('t_fechapago as fp', 'p.id_fechapago', '=', 'fp.id_fechapago')
            ->join('t_inscripcion as ins2', 'p.id_ins', '=', 'ins2.id_ins')
            ->where('ins2.id_us', $idUs)
            ->where('p.estado', 1)
            ->select([
                'p.id_pago', 'p.id_fechapago', 'p.id_ins', 'p.monto_pagado',
                'p.metodo_pago', 'p.fecha_deposito', 'p.nro_boleta_bancaria',
                'p.pago_extra', 'p.fecha_reg',
                'fp.nro_pago', 'fp.monto_a_pagar', 'fp.id_plan',
            ])
            ->orderBy('p.fecha_deposito')
            ->get()
            ->groupBy('fp.id_plan');

        $result = $inscripciones->map(function ($ins) use ($todasCuotas, $todosPagos) {
            $planId = $ins->id_plan ? (int) $ins->id_plan : null;

            $cuotas = $planId
                ? ($todasCuotas->get($planId)?->toArray() ?? [])
                : [];

            $pagos = $planId
                ? ($todosPagos->get($planId) ?? collect())
                : collect();

            $totalPlan   = collect($cuotas)->sum(fn ($c) => (float) $c->monto_a_pagar);
            $totalPagado = $pagos->sum(fn ($p) => (float) $p->monto_pagado);
            $saldo       = max(0.0, $totalPlan - $totalPagado);

            $estadoPago = match (true) {
                $totalPlan > 0 && $totalPagado >= $totalPlan => 'pagado',
                $totalPagado > 0                             => 'parcial',
                default                                      => 'pendiente',
            };

            $cuotasConEstado = collect($cuotas)->map(function ($cuota) use ($pagos) {
                $pago = $pagos->firstWhere('id_fechapago', $cuota->id_fechapago);
                return [
                    'id_fechapago'  => $cuota->id_fechapago,
                    'nro_pago'      => $cuota->nro_pago,
                    'monto_a_pagar' => (float) $cuota->monto_a_pagar,
                    'fecha_limite'  => $cuota->fecha_limite ?? null,
                    'pagado'        => (bool) $pago,
                    'monto_pagado'  => $pago ? (float) $pago->monto_pagado : null,
                    'fecha_pago'    => $pago ? $pago->fecha_deposito : null,
                    'metodo_pago'   => $pago ? $pago->metodo_pago : null,
                ];
            })->values()->all();

            return [
                'id_ins'         => $ins->id_ins,
                'nombre_programa'=> $ins->nombre_programa,
                'programa_slug'  => $ins->programa_slug,
                'periodo'        => $ins->periodo,
                'gestion'        => $ins->gestion,
                'fecha_ins'      => $ins->fecha_ins,
                'fecha_inicio'   => $ins->fecha_inicio,
                'fecha_fin'      => $ins->fecha_fin,
                'canal_venta'    => $ins->canal_venta,
                'total_plan'     => $totalPlan,
                'total_pagado'   => $totalPagado,
                'saldo'          => $saldo,
                'estado_pago'    => $estadoPago,
                'cuotas'         => $cuotasConEstado,
                'nro_pagos'      => $pagos->count(),
            ];
        })->values()->all();

        return response()->json([
            'data' => $result
        ]);
    }
}
