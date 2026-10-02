<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HistorialController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $size = (int) $request->get('pageSize', 50);
        $page = (int) $request->get('pageIndex', 1);

        $q = DB::table('t_historial');
        if ($request->has('id_us')) {
            $q->where('id_us', (int) $request->get('id_us'));
        }
        if ($request->has('id_tiporeferencia')) {
            $q->where('id_tiporeferencia', (int) $request->get('id_tiporeferencia'));
        }
        if ($request->has('id_tipohistorial')) {
            $q->where('id_tipohistorial', (int) $request->get('id_tipohistorial'));
        }
        if (! $request->boolean('conInactivos', false)) {
            $q->where('estado', 1);
        }

        $total = $q->count();
        $data = $q->orderByDesc('fecha')->offset(($page - 1) * $size)->limit($size)->get();

        return response()->json(['data' => $data, 'total' => $total]);
    }

    public function show(int $id): JsonResponse
    {
        $row = DB::table('t_historial')->where('id_historial', $id)->first();
        if (! $row) {
            abort(404, 'Historial no encontrado');
        }

        return response()->json($row);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_historial' => ['required', 'integer'],
            'id_us_reg' => ['nullable', 'integer'],
            'num_historial' => ['nullable', 'integer'],
            'id_us' => ['required', 'integer'],
            'fecha' => ['nullable', 'date'],
            'id_tiporeferencia' => ['nullable', 'integer'],
            'id_tipohistorial' => ['nullable', 'integer'],
            'documento_digital' => ['nullable', 'string', 'max:200'],
            'estado' => ['nullable', 'integer'],
        ]);
        $data['id_us_reg'] = $request->integer('id_us_reg', 0);
        $data['num_historial'] = $request->integer('num_historial', 0);
        $data['estado'] = $request->integer('estado', 1);
        $data['fecha_reg'] = now();

        DB::table('t_historial')->insert($data);

        return response()->json(DB::table('t_historial')->where('id_historial', $data['id_historial'])->first(), 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $row = DB::table('t_historial')->where('id_historial', $id)->first();
        if (! $row) {
            abort(404, 'Historial no encontrado');
        }

        $data = $request->validate([
            'fecha' => ['nullable', 'date'],
            'id_tiporeferencia' => ['nullable', 'integer'],
            'id_tipohistorial' => ['nullable', 'integer'],
            'documento_digital' => ['nullable', 'string', 'max:200'],
            'estado' => ['nullable', 'integer'],
        ]);
        DB::table('t_historial')->where('id_historial', $id)->update($data);

        return response()->json(DB::table('t_historial')->where('id_historial', $id)->first());
    }

    public function destroy(int $id): JsonResponse
    {
        DB::table('t_historial')->where('id_historial', $id)->update(['estado' => 0]);

        return response()->json(null, 204);
    }
}
