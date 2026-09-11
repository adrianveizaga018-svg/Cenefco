<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CajaController extends Controller
{
    // ── 1. Buscar estudiante por CI ──────────────────────────────────────────

    public function buscarEstudiante(Request $request): JsonResponse
    {
        $ci = trim((string) $request->get('ci', ''));
        if (strlen($ci) < 3) {
            return response()->json(null);
        }

        $usuario = DB::table('t_usuario')
            ->where('ci', $ci)
            ->orderByDesc('id_us')
            ->select([
                'id_us', 'nombre', 'appaterno', 'apmaterno',
                'email', 'celular', 'ci', 'expedido', 'genero',
            ])
            ->first();

        if (!$usuario) {
            return response()->json(null);
        }

        // Inscripciones anteriores del estudiante
        $inscripciones = DB::table('t_inscripcion as ins')
            ->join('t_imparte as imp', 'ins.id_imp', '=', 'imp.id_imp')
            ->join('t_programa as prog', 'imp.id_mat', '=', 'prog.id_programa')
            ->where('ins.id_us', $usuario->id_us)
            ->where('ins.estado', 1)
            ->select('prog.nombre_programa', 'ins.fecha_ins', 'ins.gestion', 'ins.periodo')
            ->orderByDesc('ins.fecha_ins')
            ->limit(5)
            ->get();

        return response()->json([
            'id_us'            => $usuario->id_us,
            'nombre'           => $usuario->nombre,
            'apellido_paterno' => $usuario->appaterno,
            'apellido_materno' => $usuario->apmaterno,
            'ci'               => $usuario->ci,
            'expedido'         => $usuario->expedido,
            'email'            => $usuario->email,
            'celular'          => $usuario->celular,
            'genero'           => $usuario->genero,
            'inscripciones'    => $inscripciones,
        ]);
    }

    // ── 2. Buscar programas activos con sus planes de pago ───────────────────

    public function buscarProgramas(Request $request): JsonResponse
    {
        $q = trim((string) $request->get('q', ''));

        $programas = DB::table('t_programa as prog')
            ->join('t_imparte as imp', 'imp.id_mat', '=', 'prog.id_programa')
            ->where('prog.estado_web', 'publicado')
            ->where('imp.estado', 1)
            ->when($q, fn($query) => $query->where('prog.nombre_programa', 'like', "%{$q}%"))
            ->select(
                'prog.id_programa',
                'prog.nombre_programa',
                'imp.id_imp',
                'imp.nombre as nombre_version',
                'imp.periodo',
                'imp.gestion',
                'imp.imparte_fecha_inicio',
                'imp.imparte_fecha_fin',
            )
            ->orderByDesc('imp.id_imp')
            ->limit(20)
            ->get();

        // Agrupar por programa y traer SOLO los planes habilitados para ese programa
        $result = $programas->groupBy('id_programa')->map(function ($imparticiones, $progId) {
            $first = $imparticiones->first();

            // Planes habilitados para este programa via programa_planes
            $planes = DB::table('t_plan as p')
                ->join('programa_planes as pp', 'pp.id_plan', '=', 'p.id_plan')
                ->where('pp.id_programa', $progId)
                ->where('p.estado', 1)
                ->select('p.id_plan', 'p.titulo', 'p.costo', 'p.nro_cuotas', 'p.descuento')
                ->get();

            $imparticionesData = $imparticiones->map(function ($imp) {
                return [
                    'id_imp'               => $imp->id_imp,
                    'nombre_version'       => $imp->nombre_version,
                    'periodo'              => $imp->periodo,
                    'gestion'              => $imp->gestion,
                    'imparte_fecha_inicio' => $imp->imparte_fecha_inicio,
                    'imparte_fecha_fin'    => $imp->imparte_fecha_fin,
                ];
            });

            return [
                'id_programa'     => $first->id_programa,
                'nombre_programa' => $first->nombre_programa,
                'imparticiones'   => $imparticionesData->values(),
                'planes'          => $planes->values(), // planes a nivel de programa, no de impartición
            ];
        })->values();

        return response()->json($result);
    }

    // ── 3. Registrar inscripcion presencial ──────────────────────────────────

    public function inscribir(Request $request): JsonResponse
    {
        $request->validate([
            'id_imp'            => 'required|integer',
            'id_plan'           => 'nullable|integer',
            'monto_pagado'      => 'required|numeric|min:0',
            'nro_boleta'        => 'required|string|max:200',
            'fecha_deposito'    => 'required|date',
            'metodo_pago'       => 'required|string',
            'tipo_banco_id'     => 'nullable|integer',
            'comprobante'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            // Estudiante existente
            'id_us'             => 'nullable|integer',
            'actualizar_estudiante' => 'nullable|boolean',
            // Nuevo estudiante
            'nombre'            => 'required_without:id_us|string|max:100',
            'apellido_paterno'  => 'nullable|string|max:100',
            'apellido_materno'  => 'nullable|string|max:100',
            'ci'                => 'required_without:id_us|string|max:50',
            'expedido'          => 'nullable|integer',
            'celular'           => 'nullable|string|max:20',
            'email'             => 'nullable|email|max:100',
            'genero'            => 'nullable|integer',
        ]);

        // Guardar comprobante si viene
        $comprobanteUrl = null;
        if ($request->hasFile('comprobante')) {
            $comprobanteUrl = $request->file('comprobante')
                ->store('inscripciones/comprobantes', 'public');
        }

        return DB::transaction(function () use ($request, $comprobanteUrl) {
            $idUs    = $request->input('id_us');
            $cajero  = $request->user();

            // ── Crear estudiante si no existe ────────────────────────────────
            if (!$idUs) {
                $maxId = DB::table('t_usuario')->max('id_us') ?? 0;
                $idUs  = $maxId + 1;

                DB::table('t_usuario')->insert([
                    'id_us'        => $idUs,
                    'id_us_reg'    => $cajero->id,
                    'nombre'       => $request->input('nombre'),
                    'appaterno'    => $request->input('apellido_paterno'),
                    'apmaterno'    => $request->input('apellido_materno'),
                    'ci'           => $request->input('ci'),
                    'expedido'     => $request->input('expedido'),
                    'celular'      => $request->input('celular'),
                    'email'        => $request->input('email'),
                    'genero'       => $request->input('genero', 2),
                    'password'     => Hash::make($request->input('ci')), // CI como password provisional
                    'nombre_usuario' => $request->input('ci'),
                    'estado'       => 1,
                    'fecha_reg'    => now(),
                    'tipoestudiante' => '2',
                ]);
            } else if ($request->input('actualizar_estudiante')) {
                // Actualizar estudiante si lo pide el cajero
                DB::table('t_usuario')->where('id_us', $idUs)->update([
                    'nombre'       => $request->input('nombre'),
                    'appaterno'    => $request->input('apellido_paterno'),
                    'apmaterno'    => $request->input('apellido_materno'),
                    'ci'           => $request->input('ci'),
                    'expedido'     => $request->input('expedido'),
                    'celular'      => $request->input('celular'),
                    'email'        => $request->input('email'),
                    'genero'       => $request->input('genero', 2),
                ]);
            }

            // ── Crear inscripcion ────────────────────────────────────────────
            $idIns = (DB::table('t_inscripcion')->max('id_ins') ?? 0) + 1;

            DB::table('t_inscripcion')->insert([
                'id_ins'        => $idIns,
                'id_us_reg'     => $cajero->id,
                'id_us'         => $idUs,
                'id_imp'        => $request->input('id_imp'),
                'id_plan'       => $request->input('id_plan'),
                'fecha_ins'     => now()->toDateString(),
                'fecha_reg'     => now(),
                'gestion'       => now()->year,
                'periodo'       => 'I-' . now()->year,
                'estado'        => 1,
                'canal_venta'   => 'presencial',
                'origen'        => 'caja_admin',
                'email'         => $request->input('email'),
                'telefono'      => $request->input('celular'),
            ]);

            // ── Registrar pago ───────────────────────────────────────────────
            $idPago = (DB::table('t_pago')->max('id_pago') ?? 0) + 1;

            DB::table('t_pago')->insert([
                'id_pago'            => $idPago,
                'id_us_reg'          => $cajero->id,
                'id_us'              => $idUs,
                'id_ins'             => $idIns,
                'monto_pagado'       => $request->input('monto_pagado'),
                'nro_boleta_bancaria' => $request->input('nro_boleta'),
                'fecha_deposito'     => $request->input('fecha_deposito'),
                'metodo_pago'        => $request->input('metodo_pago'),
                'tipo_banco_id'      => $request->input('tipo_banco_id'),
                'estado'             => 1,
                'estado_verificacion' => 'verificado',
                'nota_verificacion'  => 'Pago registrado en caja por ' . $cajero->nombre . ' ' . $cajero->apellido,
                'fecha_reg'          => now(),
                'pago_extra'         => 0,
                'comprobante_url'    => $comprobanteUrl,
            ]);

            return response()->json([
                'mensaje'   => 'Inscripcion registrada correctamente',
                'id_ins'    => $idIns,
                'id_us'     => $idUs,
                'id_pago'   => $idPago,
                'es_nuevo'  => !$request->input('id_us'),
            ], 201);
        });
    }

    // ── 4. Listar bancos activos ─────────────────────────────────────────────

    public function bancos(): JsonResponse
    {
        $bancos = DB::table('tipos_banco')
            ->where('activo', true)
            ->orderBy('orden')
            ->select('id', 'nombre')
            ->get();

        return response()->json($bancos);
    }
}