<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GrupoAcademicoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = $request->get('query', '');
        $size = (int) $request->get('pageSize', 50);
        $page = (int) $request->get('pageIndex', 1);

        $q = DB::table('t_grupo');
        if ($query) {
            $q->where(function ($sq) use ($query) {
                $sq->where('nombregrupo', 'like', "%{$query}%")
                    ->orWhere('siglagrupo', 'like', "%{$query}%");
            });
        }
        if ($request->has('id_test')) {
            $q->where('id_test', (int) $request->get('id_test'));
        }
        if (! $request->boolean('conInactivos', false)) {
            $q->where('estado', 1);
        }

        $total = $q->count();
        $data = $q->orderBy('nombregrupo')->offset(($page - 1) * $size)->limit($size)->get();

        return response()->json(['data' => $data, 'total' => $total]);
    }

    public function show(int $id): JsonResponse
    {
        $row = DB::table('t_grupo')->where('id_grupo', $id)->first();
        if (! $row) {
            abort(404, 'Grupo no encontrado');
        }

        return response()->json($row);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_grupo' => ['required', 'integer'],
            'id_us_reg' => ['nullable', 'integer'],
            'num_grupo' => ['nullable', 'integer'],
            'id_test' => ['nullable', 'integer'],
            'siglagrupo' => ['nullable', 'string', 'max:200'],
            'nombregrupo' => ['required', 'string', 'max:200'],
            'espacio_laboral' => ['nullable', 'string'],
            'titulo' => ['nullable', 'string', 'max:200'],
            'estado' => ['nullable', 'integer'],
        ]);
        $data['id_us_reg'] = $request->integer('id_us_reg', 0);
        $data['num_grupo'] = $request->integer('num_grupo', 0);
        $data['estado'] = $request->integer('estado', 1);
        $data['fecha_reg'] = now();

        DB::table('t_grupo')->insert($data);

        return response()->json(DB::table('t_grupo')->where('id_grupo', $data['id_grupo'])->first(), 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $row = DB::table('t_grupo')->where('id_grupo', $id)->first();
        if (! $row) {
            abort(404, 'Grupo no encontrado');
        }

        $data = $request->validate([
            'siglagrupo' => ['nullable', 'string', 'max:200'],
            'nombregrupo' => ['sometimes', 'required', 'string', 'max:200'],
            'espacio_laboral' => ['nullable', 'string'],
            'titulo' => ['nullable', 'string', 'max:200'],
            'id_test' => ['nullable', 'integer'],
            'estado' => ['nullable', 'integer'],
        ]);
        DB::table('t_grupo')->where('id_grupo', $id)->update($data);

        return response()->json(DB::table('t_grupo')->where('id_grupo', $id)->first());
    }

    public function destroy(int $id): JsonResponse
    {
        DB::table('t_grupo')->where('id_grupo', $id)->update(['estado' => 0]);

        return response()->json(null, 204);
    }
}
