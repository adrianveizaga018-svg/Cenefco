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

    // Para modulo de Dashboard de Cobranzas
    public function dashboard(Request $request): JsonResponse
    {
        $query = DB::table('t_inscripcion_cuotas as ic')
            ->join('t_inscripcion as i', 'i.id_ins', '=', 'ic.id_ins')
            ->join('t_usuario as u', 'u.id_us', '=', 'i.id_us')
            ->leftJoin('t_programa as p', 'p.id_imp', '=', 'i.id_imp')
            ->select(
                'ic.*',
                'u.nombre', 'u.appaterno', 'u.apmaterno', 'u.ci', 'u.celular',
                'p.nombre_programa'
            );

        if ($request->estado) {
            $query->where('ic.estado', $request->estado);
        }
        if ($request->ci) {
            $query->where('u.ci', 'like', '%' . $request->ci . '%');
        }

        $cuotas = $query->orderBy('ic.fecha_vencimiento', 'asc')->paginate($request->get('per_page', 20));

        return response()->json($cuotas);
    }
}
