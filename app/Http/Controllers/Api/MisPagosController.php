<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MisPagosController extends \App\Http\Controllers\Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'ci'       => ['required', 'string', 'max:20'],
            'email'    => ['required', 'email', 'max:200'],
            'expedido' => ['nullable', 'string', 'max:10'],
        ]);

        $ci       = trim($request->input('ci'));
        $email    = trim($request->input('email'));
        $expedido = $request->input('expedido');

        $usuario = DB::table('t_usuario as u')
            ->where('u.ci', $ci)
            ->where(DB::raw('LOWER(u.email)'), strtolower($email))
            ->when($expedido, fn ($q) => $q->where('u.expedido', $expedido))
            ->where('u.id_us_reg', DB::raw(
                '(SELECT MIN(u2.id_us_reg) FROM t_usuario u2 WHERE u2.ci = u.ci)'
            ))
            ->select('u.id_us', 'u.nombre', 'u.appaterno', 'u.apmaterno', 'u.ci', 'u.expedido', 'u.email', 'u.celular')
            ->first();

        if (! $usuario) {
            return response()->json(['data' => [], 'estudiante' => null]);
        }

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
            ->where('ins.id_us', $usuario->id_us)
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
            ->orderByDesc('ins.fecha_reg')
            ->limit(50)
            ->get();

        $planIds = $inscripciones->pluck('id_plan')->filter()->unique()->values()->all();

        $todasCuotas = count($planIds)
            ? DB::table('t_fechapago')
                ->whereIn('id_plan', $planIds)
                ->where('estado', 1)
                ->select('id_fechapago', 'id_plan', 'nro_pago', 'monto_a_pagar')
                ->orderBy('nro_pago')
                ->get()
                ->groupBy('id_plan')
            : collect();

        $todosPagos = DB::table('t_pago as p')
            ->leftJoin('t_fechapago as fp', 'p.id_fechapago', '=', 'fp.id_fechapago')
            ->where('p.id_us', $usuario->id_us)
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
            'estudiante' => [
                'id_us'    => $usuario->id_us,
                'nombre'   => trim("{$usuario->nombre} {$usuario->appaterno} {$usuario->apmaterno}"),
                'ci'       => $usuario->ci,
                'expedido' => $usuario->expedido,
                'email'    => $usuario->email,
                'celular'  => $usuario->celular,
            ],
            'data' => $result,
        ]);
    }
}
