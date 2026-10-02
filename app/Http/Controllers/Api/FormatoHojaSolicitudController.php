<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FormatoHojaSolicitudController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = $request->get('query', '');
        $size = (int) $request->get('pageSize', 30);
        $page = (int) $request->get('pageIndex', 1);

        $q = DB::table('t_formato_hoja_solicitud');
        if ($query) {
            $q->where('titulo', 'like', "%{$query}%");
        }
        if (! $request->boolean('conInactivos', false)) {
            $q->where('estado', 1);
        }

        $total = $q->count();
        $data = $q->orderBy('titulo')->offset(($page - 1) * $size)->limit($size)->get();

        return response()->json(['data' => $data, 'total' => $total]);
    }

    public function show(int $id): JsonResponse
    {
        $row = DB::table('t_formato_hoja_solicitud')->where('id_formato_hoja_solicitud', $id)->first();
        if (! $row) {
            abort(404, 'Formato de hoja de solicitud no encontrado');
        }

        return response()->json($row);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_formato_hoja_solicitud' => ['required', 'integer'],
            'id_us_reg' => ['nullable', 'integer'],
            'num_formato_hoja_solicitud' => ['nullable', 'integer'],
            'titulo' => ['required', 'string', 'max:200'],
            'posiciones' => ['nullable', 'string'],
            'txtdirigidoa' => ['nullable', 'string'],
            'txtasunto' => ['nullable', 'string'],
            'txtcontenido1' => ['nullable', 'string'],
            'txtpie1' => ['nullable', 'string', 'max:200'],
            'txtpie2' => ['nullable', 'string', 'max:200'],
            'estado' => ['nullable', 'integer'],
        ]);
        $data['id_us_reg'] = $request->integer('id_us_reg', 0);
        $data['num_formato_hoja_solicitud'] = $request->integer('num_formato_hoja_solicitud', 0);
        $data['estado'] = $request->integer('estado', 1);
        $data['fecha_reg'] = now();

        DB::table('t_formato_hoja_solicitud')->insert($data);

        return response()->json(DB::table('t_formato_hoja_solicitud')->where('id_formato_hoja_solicitud', $data['id_formato_hoja_solicitud'])->first(), 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $row = DB::table('t_formato_hoja_solicitud')->where('id_formato_hoja_solicitud', $id)->first();
        if (! $row) {
            abort(404, 'Formato de hoja de solicitud no encontrado');
        }

        $data = $request->validate([
            'titulo' => ['sometimes', 'required', 'string', 'max:200'],
            'posiciones' => ['nullable', 'string'],
            'txtdirigidoa' => ['nullable', 'string'],
            'txtasunto' => ['nullable', 'string'],
            'txtcontenido1' => ['nullable', 'string'],
            'txtpie1' => ['nullable', 'string', 'max:200'],
            'txtpie2' => ['nullable', 'string', 'max:200'],
            'estado' => ['nullable', 'integer'],
        ]);
        DB::table('t_formato_hoja_solicitud')->where('id_formato_hoja_solicitud', $id)->update($data);

        return response()->json(DB::table('t_formato_hoja_solicitud')->where('id_formato_hoja_solicitud', $id)->first());
    }

    public function destroy(int $id): JsonResponse
    {
        DB::table('t_formato_hoja_solicitud')->where('id_formato_hoja_solicitud', $id)->update(['estado' => 0]);

        return response()->json(null, 204);
    }
}
