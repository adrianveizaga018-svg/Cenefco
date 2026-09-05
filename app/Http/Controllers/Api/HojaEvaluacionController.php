<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HojaEvaluacionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $size = (int) $request->get('pageSize', 30);
        $page = (int) $request->get('pageIndex', 1);

        $q = DB::table('t_hojaevaluacion');
        if ($request->has('id_us')) {
            $q->where('id_us', (int) $request->get('id_us'));
        }
        if ($request->has('id_us_tut')) {
            $q->where('id_us_tut', (int) $request->get('id_us_tut'));
        }
        if (! $request->boolean('conInactivos', false)) {
            $q->where('estado', 1);
        }

        $total = $q->count();
        $data = $q->orderByDesc('id_hojaevaluacion')->offset(($page - 1) * $size)->limit($size)->get();

        return response()->json(['data' => $data, 'total' => $total]);
    }

    public function show(int $id): JsonResponse
    {
        $row = DB::table('t_hojaevaluacion')->where('id_hojaevaluacion', $id)->first();
        if (! $row) {
            abort(404, 'Hoja de evaluación no encontrada');
        }

        return response()->json($row);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_hojaevaluacion' => ['required', 'integer'],
            'id_us_reg' => ['nullable', 'integer'],
            'num_hojaevaluacion' => ['nullable', 'integer'],
            'id_us' => ['required', 'integer'],
            'titulo_trabajo' => ['nullable', 'string'],
            'id_us_tut' => ['nullable', 'integer'],
            'nombre_secretario' => ['nullable', 'string', 'max:200'],
            'lugar_y_fecha' => ['nullable', 'string', 'max:200'],
            'id_us_tribpres' => ['nullable', 'integer'],
            'id_us_trib1' => ['nullable', 'integer'],
            'id_us_trib2' => ['nullable', 'integer'],
            'id_us_trib3' => ['nullable', 'integer'],
            'estado' => ['nullable', 'integer'],
        ]);
        $data['id_us_reg'] = $request->integer('id_us_reg', 0);
        $data['num_hojaevaluacion'] = $request->integer('num_hojaevaluacion', 0);
        $data['estado'] = $request->integer('estado', 1);
        $data['fecha_reg'] = now();

        DB::table('t_hojaevaluacion')->insert($data);

        return response()->json(DB::table('t_hojaevaluacion')->where('id_hojaevaluacion', $data['id_hojaevaluacion'])->first(), 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $row = DB::table('t_hojaevaluacion')->where('id_hojaevaluacion', $id)->first();
        if (! $row) {
            abort(404, 'Hoja de evaluación no encontrada');
        }

        $data = $request->validate([
            'titulo_trabajo' => ['nullable', 'string'],
            'id_us_tut' => ['nullable', 'integer'],
            'nombre_secretario' => ['nullable', 'string', 'max:200'],
            'lugar_y_fecha' => ['nullable', 'string', 'max:200'],
            'id_us_tribpres' => ['nullable', 'integer'],
            'id_us_trib1' => ['nullable', 'integer'],
            'id_us_trib2' => ['nullable', 'integer'],
            'id_us_trib3' => ['nullable', 'integer'],
            'estado' => ['nullable', 'integer'],
        ]);
        DB::table('t_hojaevaluacion')->where('id_hojaevaluacion', $id)->update($data);

        return response()->json(DB::table('t_hojaevaluacion')->where('id_hojaevaluacion', $id)->first());
    }

    public function destroy(int $id): JsonResponse
    {
        DB::table('t_hojaevaluacion')->where('id_hojaevaluacion', $id)->update(['estado' => 0]);

        return response()->json(null, 204);
    }
}
