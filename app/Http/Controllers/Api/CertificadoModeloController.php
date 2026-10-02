<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CertificadoModeloController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = $request->get('query', '');
        $size = (int) $request->get('pageSize', 30);
        $page = (int) $request->get('pageIndex', 1);

        $q = DB::table('t_certificadomodelo');
        if ($query) {
            $q->where('nombre_certificadomodelo', 'like', "%{$query}%");
        }
        if (! $request->boolean('conInactivos', false)) {
            $q->where('estado', 1);
        }

        $total = $q->count();
        $data = $q->orderBy('nombre_certificadomodelo')->offset(($page - 1) * $size)->limit($size)->get();

        return response()->json(['data' => $data, 'total' => $total]);
    }

    public function show(int $id): JsonResponse
    {
        $row = DB::table('t_certificadomodelo')->where('id_certmod', $id)->first();
        if (! $row) {
            abort(404, 'Modelo de certificado no encontrado');
        }

        return response()->json($row);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_certmod' => ['required', 'integer'],
            'id_us_reg' => ['nullable', 'integer'],
            'num_certmod' => ['nullable', 'integer'],
            'nombre_certificadomodelo' => ['required', 'string', 'max:200'],
            'textocertificado' => ['nullable', 'string'],
            'estado' => ['nullable', 'integer'],
        ]);
        $data['id_us_reg'] = $request->integer('id_us_reg', 0);
        $data['num_certmod'] = $request->integer('num_certmod', 0);
        $data['estado'] = $request->integer('estado', 1);
        $data['fecha_reg'] = now();

        DB::table('t_certificadomodelo')->insert($data);

        return response()->json(DB::table('t_certificadomodelo')->where('id_certmod', $data['id_certmod'])->first(), 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $row = DB::table('t_certificadomodelo')->where('id_certmod', $id)->first();
        if (! $row) {
            abort(404, 'Modelo de certificado no encontrado');
        }

        $data = $request->validate([
            'nombre_certificadomodelo' => ['sometimes', 'required', 'string', 'max:200'],
            'textocertificado' => ['nullable', 'string'],
            'estado' => ['nullable', 'integer'],
        ]);
        DB::table('t_certificadomodelo')->where('id_certmod', $id)->update($data);

        return response()->json(DB::table('t_certificadomodelo')->where('id_certmod', $id)->first());
    }

    public function destroy(int $id): JsonResponse
    {
        DB::table('t_certificadomodelo')->where('id_certmod', $id)->update(['estado' => 0]);

        return response()->json(null, 204);
    }
}
